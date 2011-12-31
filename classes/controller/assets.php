<?php

/**
 * Main controller
 *
 * @package  Assets
 * @author   Alex Little (alxlit.name)
 * @license  MIT
 */
class Controller_Assets extends Controller {

  public function action_serve()
  {
    $sources = Assets::find($target = $this->request->param('target'));

    if ($sources)
    {
      $path = Assets::$config->targets.$target;

      // Create parent directories (as necessary)
      if (is_dir($dir = dirname($path)) || mkdir($dir, 0777, TRUE))
      {
        $result = FALSE;

        foreach ($sources as $type => $source)
        {
          if ( ! $type)
          {
            // Simple, single-source asset with no compilation step. Just link
            // to it and we're done.
            symlink($source[0], $path);
          }
          else if (is_callable($fn = "Assets::compile_$type"))
          {
            if ( ! $result)
            {
              $result = '';
            }

            $result.= call_user_func($fn, $source);
          }
          else
          {
            throw new HTTP_Exception_500("Missing compiler for type '$type'");
          }
        }

        if ($result !== FALSE)
        {
          file_put_contents($path, $result);
        }

        if (is_file($path) || is_link($file))
        {
          // Success!
          $this->request->redirect($this->request->uri());
        }
      }
    }

    throw new HTTP_Exception_404();
  }

}

?>
