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
should begin with  "compile\_." It takes an array containing the source file(s)
for the asset and should return the **contents** of the final, compiled asset.

    class Assets extends Kohana_Assets {

      function compile_png(array $files)
      {
        // Create a temporary location to store the optimized image
        $tmp = tempnam('/tmp/', '');

        // Since it doesn't make sense to compile multiple PNGs into a single image,
        // there should only ever be one source file...
        exec('optipng '.$files[0].' -out '.$tmp);

        // Return image
        return file_get_contents($tmp);
      }

    }

**Step 3**: Clear the `assets.targets` directory.


