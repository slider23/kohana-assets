<?php

/**
 * Compilers and other helper functions.
 *
 * @package  Kohana/Assets
 * @author   Alex Little
 */
class Kohana_Assets {

  static $config;

  static function compile_coffee($source)
  {
    throw new Exception('CoffeeScript not yet supported');
  }

  static function compile_css($source)
  {
    self::vendor('cssmin');

    return CssMin::minify( file_get_contents($source) );
  }

  static function compile_js($source)
  {
    self::vendor('jsminplus');

    // Strips end semi-colons, which can break multi-source assets; should try
    // to find a better solution for this.
    return JSMinPlus::minify( file_get_contents($source) ).';';
  }

  static function compile_less($source)
  {
    self::vendor(array('lessphp/lessc.inc', 'cssmin'));

    $less = new lessc();

    $less->importDisabled = FALSE;
    $less->importDir = dirname($source);

    return CssMin::minify( $less->parse( file_get_contents($source) ) );
  }

  /**
   * Find the source files for a target asset.
   *
   * @param   string  Target asset (e.g. css/style.css)
   *
   * @return  array   Array containing the target's source files, or FALSE
   */
  static function find_sources($target)
  {
    $target = pathinfo($target);

    $target += array
    (
      // Full path without the extension
      'pathname' => "{$target['dirname']}/{$target['filename']}",

      // Target type
      'type' => self::get_type($target['extension'])
    );

    $source = array
    (
      // Directory that will contain the source file(s)
      'dirname' => self::$config->source_dir.$target['dirname'],

      // Possible extension(s)
      'extension' => $target['extension'],

      // Possible type(s)
      'type' => (array) Arr::get(self::$config->target_types, $target['type']),
    );

    if ($source['type'])
    {
      // It's a known type, so there is a compilation step possibly involving
      // multiple sources of different types
      $source['extension'] = self::get_type_ext($source['type']);

      if (in_array($target['pathname'], self::$config->concatable))
      {
        foreach (Kohana::include_paths() as $dir)
        {
          if (is_dir($dir.= $source['dirname'].'/'.$target['filename']))
          {
            // Multiple sources
            return self::ls($dir, $source['extension']);
          }
        }
      }
    }

    foreach ((array) $source['extension'] as $ext)
    {
      if ($ext{0} === '.')
      {
        $ext = substr($ext, 1);
      }

      if ($file = Kohana::find_file($source['dirname'], $target['filename'], $ext))
      {
        // Single source
        return array($file);
      }
    }

    return FALSE;
  }

  /**
   * Determine the type given a file extension.
   */
  static function get_type($ext)
  {
    if ($ext{0} !== '.')
    {
      $ext = ".{$ext}";
    }

    foreach (self::$config->types as $type => $extensions)
    {
      if (in_array($ext, $extensions))
      {
        return $type;
      }
    }

    return NULL;
  }

  /**
   * Get the extension(s) for the given type(s).
   */
  static function get_type_ext($types)
  {
    $ext = array();

    foreach ((array) $types as $type)
    {
      $ext = array_merge($ext, Arr::get(self::$config->types, $type, array()));
    }

    return $ext;
  }

  /**
   * Check for modifications (if enabled) and set asset route.
   */
  static function init()
  {
    self::$config = Kohana::$config->load('assets');

    if (self::$config->watch)
    {
      foreach (self::ls(self::$config->target_dir, NULL, TRUE) as $asset)
      {
        // Delete assets whose source files have changed (they'll be recompiled
        // the next time they are requested).
        self::modified($asset) && unlink($asset);
      }
    }

    $dir = basename(self::$config->target_dir);

    // Set route.
    Route::set('assets', "{$dir}/<target>", array('target' => '.+'))
      ->defaults(array(
          'controller' => 'assets',
          'action'     => 'serve'
        ));
  }

  /**
   * List files in a directory. Optionally filter for file extensions and 
   * recurse into sub-directories.
   *
   * @param  string
   * @param  array
   * @param  boolean
   *
   * @return  array  List of files
   */
  static function ls($dir, array $extensions = NULL, $recurse = FALSE)
  {
    $files = array();

    try
    {
      foreach (new DirectoryIterator($dir) as $file)
      {
        if ($file->isFile())
        {
          $ext = '.'.pathinfo($file->getFilename(), PATHINFO_EXTENSION);

          if ($extensions === NULL || in_array($ext, $extensions))
          {
            $files[] = $file->getPathname();
          }
        }
        else if ($file->isDir() && ! $file->isDot() && $recurse)
        {
          $files = array_merge($files, self::ls($file->getPathname(), $extensions, TRUE));
        }
      }
    }
    catch (Exception $e)
    {
      return FALSE;
    }

    return $files;
  }

  /**
   * Check whether the source files for an asset have been modified since the
   * last time they were compiled.
   *
   * @param  string
   *
   * @return  boolean
   */
  static function modified($target)
  {
    if (is_file($target))
    {
      $target_modified = filemtime($target);

      // Find source files
      $sources = self::find_sources( substr($target, strlen(self::$config->target_dir)) );

      foreach ((array) $sources as $source)
      {
        if (filemtime($source) > $target_modified)
        {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

  /**
   */
  static function vendor($files)
  {
    foreach ((array) $files as $file)
    {
      require_once Kohana::find_file('vendor', $file);
    }
  }

}

?>
