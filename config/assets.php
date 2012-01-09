<?php

return array
(
  'sources' => APPPATH.'assets/',
  'targets' => DOCROOT.'assets/',

  // Enable watching source files for modifications.
  'watch' => Kohana::$environment === Kohana::DEVELOPMENT,

  // Specify source folders that can be compiled and sent as a single asset.
  // Note that individual files can still be accessed.
  'compile_folders' => array(),

  // Source types; these require compilation into one of the target_types and
  // cannot be accessed directly.
  'types' => array
  (
    'coffee'    => array('.coffee'),
    'css'       => array('.css'),
    'js'        => array('.js'),
    'less'      => array('.less'),
  ),

  // After compiling, the target asset will end up being one of these types.
  'target_types' => array
  (
    'css' => array('css', 'less'),
    'js'  => array('js', 'coffee'),
  ),

  // Vendor library paths.
  'vendor' => array
  (
    'cssmin'    => MODPATH.'assets/vendor/cssmin.php',
    'jsmin'     => MODPATH.'assets/vendor/jsmin.php',
    'jsminplus' => MODPATH.'assets/vendor/jsminplus.php',
    'lessphp'   => MODPATH.'assets/vendor/lessphp/lessc.inc.php'
  )
);

?>
