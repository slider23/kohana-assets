# Usage

After [installing](install) and [setting up](config), simply drop your assets
into the `assets.sources` directory and you're ready to go. 

A quick example with the default config:

`APPPATH/assets/`:

    css/
      style.less
    js/
      lib/
        jquery.js
        jquery.plugin.js
      init.js

`APPPATH/views/template.php`:

    <!doctype html>
    <html>
      <head>
        <title>Foo Bar, LLC</title>
        <?= HTML::style('assets/css/style.css') ?>

        <?= HTML::script('assets/js/jquery.js') ?>
        <?= HTML::script('assets/js/jquery.plugin.js') ?>
        <?= HTML::script('assets/js/init.js') ?>
      </head>
      <body>
      ...
      </body>
    </html>


## Multi-source assets

Sometimes it's possible to really speed up your app by combining assets into a
single large file rather than having to send multiple smaller ones. With
kohana-assets, you can specify folders that can be compiled into a single 
asset in `assets.compile_folders`. An example:

`APPPATH/assets/`:

    js/
      foo_chat/
        colors.js
        commands.js
        event.js
        user.js
        video.js

`APPPATH/config/assets.php`:

    'compile_folders' => array('js/foo_chat/')

Then the asset `js/foo_chat.js` can be requested, and it'll consist of all the
JavaScript (and CoffeeScript, if there were any) files in `APPPATH/assets/js/foo_chat`
(but **not** in any subfolders).

Note that individual files can still be accessed (e.g. `js/foo_chat/colors.js`).

## Caveats

  - Note the source files for multi-source assets that use the `compile_folders`
    option are compiled in ***no particular order***. For CSS this is unacceptable,
    and should be kept in mind for JavaScript projects.

  - In most cases it is preferable to use a compiler that uses directives when
    combining sources (e.g. LESS can combine files based on `@import`).
    **However**, there is currently no way for kohana-assets itself to know 
    what those directives are, so its `watch` mode will not work as expected.


