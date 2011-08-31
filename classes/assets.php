<?php

/**
 * Helper functions.
 *
 * @package  Assets
 * @author   Alex Little (alxlit.github.com)
 * @version  0.1
 */
class Assets {

  static $config, $vendor;

  /**
   * Compile a CSS asset.
   *
   * @param   array   $source   Asset source files.
   * @param   string  $output   Output is appended to this string.
   *
   * @return  string  The compiled CSS asset.
   */
  static function compile_css(array $source, $output = '')
  {
    require_once self::$vendor->cssmin;

    for ($i = 0; $i < count($source); $i++)
    {
      $dir = dirname($source[$i]);
      $raw = file_get_contents($source[$i]);

      if (in_array(self::ext($source[$i]), self::$config->ext['less']))
      {
        require_once self::$vendor->lessphp;

        $less = new lessc();

        // Enable merging @imports (see below).
        $less->importDisabled = FALSE;
        $less->importDir = $dir;

        $raw = $less->parse($raw);
      }

      // Minify the output. Note that @imports are enabled, but with the caveat
      // that they can't be checked for modifications.
      $output.= CssMin::minify($raw, array('ImportImports' => array('BasePath' => $dir)));
    }

    return $output;
  }

  /**
   * Compile a JavaScript asset.
   */
  static function compile_js(array $source, $output = '')
  {
    require_once self::$vendor->jsmin;

    for ($i = 0; $i < count($source); $i++)
    {
      $output.= file_get_contents($source[$i]);
    }

    return JSMin::minify($output);
  }

  /**
   * Get the file extension from a file path. Optionally, also return the
   * original path without it.
   */
  static function ext($path, $ext_only = TRUE)
  {
    $ext = '.'.pathinfo($path, PATHINFO_EXTENSION);

    if ($ext)
    {
      $path = substr($path, 0, -strlen($ext));
    }

    return $ext_only ? $ext : array($path, $ext);
  }

  /**
   * Find an asset.
   *
   * @return  mixed   FALSE or array( type, array sources )
   */
  static function find($target)
  {
    $source = FALSE;

    // The asset type.
    $type = NULL;

    // Source path and extension.
    list($path, $ext) = self::ext(self::$config->source_dir.$target, FALSE);

    if ($ext)
    {
      $type = substr($ext, 1);

      if (isset(self::$config->ext[$type]))
      {
        $ext = array_merge(array($ext), self::$config->ext[$type]);
      }

      foreach ((array) $ext as $v)
      {
        if (is_file($path.$v))
        {
          // Test for source files that may have a different extension than
          // what the final asset has, but are nonetheless part of it.
          $source = array($path.$v);

          break;
        }
      }

      // Couldn't find any files. Perhaps it's a directory.
      if ( ! $source && is_dir($path))
      {
        $source = self::ls($path, (array) $ext);
      }
    }

    return array($source, $type);
  }

  /**
   * Initialize.
   */
  static function init()
  {
    self::$config = Kohana::$config->load('assets');

    // Shortcut to vendor locations.
    self::$vendor = (object) self::$config->vendor;

    if (self::$config->check_assets)
    {
      // Assets to check.
      $assets = self::ls(self::$config->target_dir, NULL, TRUE);

      for ($i = 0; $i < count($assets); $i++)
      {
        if (self::modified($assets[$i]))
        {
          // The asset has been modified. Delete it so that next time it's
          // requested it'll be recompiled.
          unlink($assets[$i]);
        }
      }
    }

    $dir = basename(self::$config->target_dir);

    // Set route.
    Route::set('assets', $dir.'/<target>', array('target' => '.+'))
      ->defaults(array(
          'controller' => 'assets',
          'action'     => 'serve'
        ));
  }

  /**
   * List the files in a directory. Optionally, filter by file extension and
   * recurse into sub-directories.
   *
   * @param   string    $dir  The directory.
   * @param   array     $ext  List only files that have these extensions.
   * @param   boolean   $r    List files recursively.
   *
   * @return  array   List of files in the directory.
   */
  static function ls($dir, array $ext = NULL, $r = FALSE)
  {
    $files = array();

    try
    {
      foreach (new DirectoryIterator($dir) as $file)
      {
        if ($file->isFile())
        {
          if ($ext === NULL || in_array(self::ext($file->getFilename()), $ext))
          {
            $files[] = $file->getPathname();
          }
        }
        elseif ($file->isDir() && ! $file->isDot() && $r)
        {
          // Recurse into sub-directories.
          $files = array_merge($files, self::ls($file->getPathname(), $ext, TRUE));
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
   * Check if the asset is ready to be served by checking whether any of the
   * sources have been modified.
   *
   * @param   string    $target   The target asset (path).
   * @param   array     $souce    The target's source files (optional).
   *
   * @return  boolean   Whether the souce files have been modified since the 
   *                    target was last compiled.
   */
  static function modified($target, array $source = NULL)
  {
    if (is_file($target))
    {
      $target_modified = filemtime($target);

      if ($source === NULL)
      {
        // Sources not given, find them.
        list($source, ) = self::find(substr($target, strlen(self::$config->target_dir)));
      }

      for ($i = 0; $i < count($source); $i++)
      {
        if (filemtime($source[$i]) > $target_modified)
        {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

}

?>
