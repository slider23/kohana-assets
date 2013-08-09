# kohana-assets

Easy & efficient asset management for Kohana 3.

## Features

  - Out-of-the-box support for CSS ([CssMin](https://code.google.com/p/cssmin/)), 
    LESS ([lessphp](http://leafo.net/lessphp/)), JavaScript ([JsMinPlus](https://code.google.com/p/minify)), 
    and CoffeeScript (eventually).
  - Easy to use and extend
  - Efficient; assets are compiled once and served directly thereafter
  - Watch mode (checks for source modifications)
  - Makes full use of Kohana's cascading file system when sourcing assets
  - Multiple source files into a single asset

## See also

There are plenty of asset managers for Kohana. Depending on your needs you might
find [one of these](https://github.com/search?type=Repositories&language=PHP&q=kohana-assets)
preferable.

# Overview

**kohana-assets** comes out-of-the-box with support for CSS, LESS, JavaScript,
and CoffeeScript (eventually). It can be [extended](extending) to add or change
the compilation step for these and other types of assets.

## Request Flow

  1. If the asset has already been compiled, it will exist in the `targets`
     (see [configuration](config))  directory and be served directly by Apache.
  2. If the asset is one of `target_types`, look for its source file(s) using
     `Kohana::find_file()`, compile, save, and redirect to the compiled asset (1).
  3. If the asset is one of `types`, the client is trying to access a
     source file directly, which is not allowed. Otherwise, simply link to
     the asset if it exists (no compilation step).

# Install

Besides putting the module in `MODPATH` and enabling it in `bootstrap.php,`
you should:

  1. Create `APPPATH/assets/`
  2. Create `DOCROOT/assets/`, and make sure PHP has permission to write to it
  3. Make sure that in your server configuration files that exist are served
     directly (e.g. `RewriteCond %{REQUEST_FILENAME} !-f` before rewriting to
     index.php for Apache servers).
  3. Copy the config file to your application

# Configuration

Type      | Option           | Description                | Default value
----------|------------------|----------------------------| -------------------------
`string`  | source\_dir      | Source directory (relative)| `assets/`
`string`  | target\_dir      | Target directory           | `DOCROOT/assets/`
`array`   | concatable       | Source folders that can be compiled into a single asset (see [usage](usage)) | `array()`
`array`   | types            | Source types that require a compilation step and their associated file extension(s) | See [extending](extending)
`array`   | target\_types    | Target types and their associated source types | See [extending](extending)
`boolean` | watch            | Check for source file changes and recompile as necessary | `Kohana::$environment === Kohana::DEVELOPMENT`

# Usage

After [installing](install) and [setting up](config), simply drop your assets
into `source_dir` in your application and you're ready to go.

A quick example with the default config:

`APPPATH/assets/`:

    css/
      style.less
    js/
      lib/
        jquery.js
        jquery.plugin.js
      main.js

`APPPATH/views/template.php`:

    <!doctype html>
    <html>
      <head>
        <?= HTML::style('assets/css/style.css') ?>

        <title>Foo Bar, LLC</title>

        <?= HTML::script('assets/js/lib/jquery.js') ?>
        <?= HTML::script('assets/js/lib/jquery.plugin.js') ?>
        <?= HTML::script('assets/js/main.js') ?>
      </head>
      <body>
      </body>
    </html>


## Multi-source assets

Sometimes it is possible to really speed up your app by combining assets into
a single file, rather than having multiple smaller ones. In this regard
kohana-assets provides some very simplistic functionality by allowing folders
specified in `concatable` to be compiled and concatenated into a single asset.
For example:

`APPPATH/assets/`:

    js/
      foo_chat/
        colors.js
        commands.js
        event.js
        user.js
        video.js

`APPPATH/config/assets.php`:

    'concatable' => array('js/foo_chat')

Then the asset `js/foo_chat.js` can be requested, and it'll consist of all the
JavaScript (and CoffeeScript, if there were any) files in `APPPATH/assets/js/foo_chat`
(but **not** in any subfolders).

Individual files can still be accessed (e.g. `js/foo_chat/colors.js`).

## Caveats

  - Files in `concatable` folders are concatenated in **no particular order**.
    For CSS this is unacceptable, and should be kept in mind if you plan to use
    it for JavaScript projects.

  - For the above reason it is almost always preferable to have a compiler do
    it based on directives in the source code (e.g. LESS can combine files
    based on `@import`). **However, there is currently no way for compilers to
    communicate those directives back to kohana-assets**, so the `watch` mode
    will not work as expected.

# Extending

**kohana-assets** can easily be extended to add or modify asset types.

In the (untested) example below we add an optimizer for our PNG images. This
case is a bit different from the rest since a). we aren't dealing with text,
and b). we're using an external program.

**Step 1**: Specify the PNG asset type in `APPPATH/config/assets.php`:

    'types' => array(
      'png' => array('.png')
    ),

    'target_types' => array(
        'png' => array('png')
    )

**Step 2**: Create the compiler in `APPPATH/classes/assets.php`. The compiler
must: 1). Be prefixed with `compile_`, 2). Take the source file path as a parameter,
and 3). Return the **contents** of the final, compiled asset.

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

**Step 3**: Clear `target_dir` to make sure existing PNGs are compiled.