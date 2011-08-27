<?php

return array(

  // Check if asset source files have been modified since the last time 
  // Assets::init was called.
  'check_assets' => Kohana::$environment === Kohana::DEVELOPMENT,

  'ext' => array(

    // File types that might compile into a stylesheet.
    'css'  => array('.less', '.css'),

    'js'   => array('.js'),
    'less' => array('.less'),

  ),

  'source_dir' => APPPATH.'assets/',

  // This must be a directory in the DOCROOT, otherwise routing will mess up.
  'target_dir' => DOCROOT.'assets/',

  // Vendor library shortcuts.
  'vendor' => array(
    'cssmin'  => MODPATH.'assets/vendor/cssmin.php',
    'jsmin'   => MODPATH.'assets/vendor/jsmin.php',
    'lessphp' => MODPATH.'assets/vendor/lessphp/lessc.inc.php',
  )

);

?>
