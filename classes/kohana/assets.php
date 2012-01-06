<?php

/**
 * Helper functions
 *
 * @author   Alex Little (alxlit.name)
 * @package  Assets
 */
class Kohana_Assets {

  static $config;

  static function compile_coffee($source)
  {
    throw new Exception('CoffeeScript not yet supported');
  }

  static function compile_css(array $files)
  {
    require_once self::$config->vendor['cssmin'];

    $result = '';

    foreach ($files as $f)
    {
      $result.= CssMin::minify(file_get_contents($f));
    }

    return $result;
  }

  static function compile_js(array $files)
  {
    require_once self::$config->vendor['jsmin'];

    $result = '';

    foreach ($files as $f)
    {
      $result.= file_get_contents($f);
    }

    return JSMin::minify($result);
  }

  static function compile_less(array $files)
  {
    require_once self::$config->vendor['lessphp'];
    require_once self::$config->vendor['cssmin'];

    $less = new lessc();

    $result = '';

    foreach ($files as $f)
    {
      $less->importDisabled = FALSE;
      $less->importDir = dirname($f);

      $result.= $less->parse(file_get_contents($f));
    }

    return CssMin::minify($result);
  }

  // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  /**
   * Find the source files for a target asset.
   *
   * @param  string  $target  Requested asset (e.g. assets/css/default.css)
   *
   * @return  array  List of source files array(type => files) or FALSE.
   */
  static function find($target)
  {
    $path = pathinfo($target);
    $path['pathname'] = "{$path['dirname']}/{$path['filename']}";

    $cfg = self::$config;

    $sources = FALSE;

    if ($type = self::type('.'.$path['extension']))
    {
      if (isset($cfg->target_types[$type]))
      {
        $source_path = "{$cfg->sources}/{$path['pathname']}";

        // Try to find source file from known extensions
        foreach ($source_ext = self::ext($cfg->target_types[$type]) as $ext)
        {
          if (is_file($f = $source_path.$ext))
          {
            $sources = $f;
            break;
          }
        }

        if ( ! $sources
          && is_dir($source_path)
          && in_array($path['pathname'], $cfg->compile_folders) )
        {
          $sources = self::ls($source_path, $source_ext);
        }
      }
    }
    else if (is_file($f = $cfg->sources.$target))
    {
      // No compilation step
      $sources = $f;
    }

    if ($sources)
    {
      $tmp = array();

      foreach ((array) $sources as $source)
      {
        $ext = '.'.pathinfo($source, PATHINFO_EXTENSION);

        // Organize by type
        $tmp[self::type($ext)][] = $source;
      }

      $sources = $tmp;
    }

    return $sources;
  }

  /**
   * Get the file extension(s) for the given type(s).
   */
  static function ext($types)
  {
    $ext = array();

    foreach ((array) $types as $type)
    {
      $ext = array_merge($ext, self::$config->types[$type]);
    }

    return $ext;
  }

  /**
   * Determine the type given a file extension.
   */
  static function type($ext)
  {
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
   * Check for modifications (if enabled) and set asset route.
   */
  static function init()
  {
    self::$config = Kohana::$config->load('assets');

    if (self::$config->watch)
    {
      $assets = self::ls(self::$config->targets, NULL, TRUE);

      foreach ($assets as $asset)
      {
        if (self::modified($asset))
        {
          // The asset has been modified; delete it (it'll be recompiled next
          // time it is requested)
          unlink($asset);
        }
      }
    }

    $dir = basename(self::$config->targets);

    // Set route.
    Route::set('assets', "{$dir}/<target>", array('target' => '.+'))
      ->defaults(array(
          'controller' => 'assets',
          'action'     => 'default'
        ));
  }

  /**
   * List files in a directory. Optionally filter for file extensions and 
   * recurse into sub-directories.
   *
   * @param  string   $dir
   * @param  array    $extensions
   * @param  boolean  $recurse
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
   * @param  string  $target
   *
   * @return  boolean
   */
  static function modified($target)
  {
    if (is_file($target))
    {
      $target_modified = filemtime($target);

      // Fetch source files
      $sources = self::find(substr($target, strlen(self::$config->targets)));

      if ($sources)
      {
        foreach (Arr::flatten($sources) as $source)
        {
          if (filemtime($source) > $target_modified)
          {
            return TRUE;
          }
        }
      }
    }

    return FALSE;
  }

}

?>
