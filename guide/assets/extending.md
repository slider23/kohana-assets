# Extending

**kohana-assets** can easily be extended to add your own types or to change the 
included compilers.

In the untested example below we add a compilation step for PNG images. This case
is a bit different since **a)**. we aren't dealing with text, and **b)**. we're 
using an external program.

**Step 1**: Specify the PNG asset type in `APPPATH/config/assets.php`:

    'types' => array(
      ...
      'png' => array('.png')
    ),

    'target_types' => array(
      ...
      'png' => array('png')
    )

**Step 2**: Create the compiler in `APPPATH/classes/assets.php`. The compiler
should begin with  "compile\_." It should take the source file and return the
**contents** of the final, compiled asset.

    class Assets extends Kohana_Assets {

      function compile_png($source)
      {
        // Create a temporary location to store the optimized image
        $tmp = tempnam('/tmp/', '');

        // Run
        exec('optipng '.$source.' -out '.$tmp);

        // Return image
        return file_get_contents($tmp);
      }

    }

**Step 3**: Clear `target_dir` so that everything gets recompiled.


