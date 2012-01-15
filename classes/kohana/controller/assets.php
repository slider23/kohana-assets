<?php

/**
 * Main controller.
 *
 * @package  Assets
 * @author   Alex Little
 */
class Kohana_Controller_Assets extends Controller {

  /**
   */
  public function action_serve()
  {
    $sources = Assets::find_sources( $target = $this->request->param('target') );

    if ($sources)
    {
      $target = Assets::$config->target_dir.$target;

      // Create parent directories
      if (is_dir($dir = dirname($target)) || mkdir($dir, 0777, TRUE))
      {
        $result = FALSE;

        foreach ($sources as $source)
        {
          $type = Assets::get_type(pathinfo($source, PATHINFO_EXTENSION));

          if ( ! $type)
          {
            // Simple, single-source asset with no compilation step. Just link
            // to it and we're done.
            symlink($source, $target);
          }
          else if (is_callable($fn = "Assets::compile_{$type}"))
          {
            $result.= call_user_func($fn, $source);
          }
          else
          {
            throw new Kohana_Exception('Missing compiler for asset type :type', array('type' => $type));
          }
        }

        if ($result !== FALSE)
        {
          file_put_contents($target, $result);
        }

        if (is_file($target) || is_link($target))
        {
          // Success!
          $this->request->redirect( $this->request->uri() );
        }
      }
    }

    throw new HTTP_Exception_404();
  }

}

?>
