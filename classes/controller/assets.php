<?php

/**
 * Main controller.
 *
 * @package   Assets
 * @author    Alex Little (http://alxlit.github.com/)
 */
class Controller_Assets extends Controller {

  public function action_serve()
  {
    list($source, $type) = Assets::find($target = $this->request->param('target'));

    if ($source)
    {
      $path = Assets::$config->target_dir . $target;

      // Check if the parent directory of the asset exists, if not we need to
      // create it.
      $dir = dirname($path);

      if (is_dir($dir) || mkdir($dir, 0777, TRUE))
      {
        // Asset compiler.
        $compiler = 'Assets::compile_'.$type;

        if (is_callable($compiler))
        {
          $output = call_user_func($compiler, $source);

          // Write output.
          file_put_contents($path, $output);
        }
        else
        {
          // Presumably the asset doesn't need/want to be compiled, so it's
          // enough to just link it.
          symlink($source[0], $path);
        }
      }

      if (is_file($path) || is_link($path))
      {
        // The asset now exists, and assuming that the path to this controller
        // and the asset path are the same, it'll be served directly.
        $this->request->redirect($this->request->uri());
      }
    }

    throw new HTTP_Exception_404();
  }

}

?>
