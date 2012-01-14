<?php

return array
(
  'sources' => APPPATH.'assets/',
  'targets' => DOCROOT.'assets/',

  'watch' => Kohana::$environment === Kohana::DEVELOPMENT,

  'compile_folders' => array(),

  'types' => array(
    'coffee' => array('.coffee'),
    'css'    => array('.css'),
    'js'     => array('.js'),
    'less'   => array('.less'),
  ),

  'target_types' => array(
    'css' => array('css', 'less'),
    'js'  => array('js', 'coffee'),
  ),
);

?>
