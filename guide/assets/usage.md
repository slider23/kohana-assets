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
kohana-assets provides some very simplistic functionality by allows folders 
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

