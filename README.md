kohana-assets
=============

Another application assets manager for Kohana 3.2. This one aims to be really
useful and easy to use.

Features
--------

  - [LESS CSS](http://leafo.net/lessphp/) support
  - CSS and JavaScript minification

I plan on adding CoffeeScript support whenever the PHP port gets finished. If
your server already supports a command-line option (JCoffeeScript or coffee.bat
or whatever) you could probably patch that in pretty easily, too.

Installation
------------

  1. Move the contents of this folder to `MODPATH/assets/`.
  2. Copy `MODPATH/assets/config/assets.php` to `APPPATH/config`.
  3. Enable the module in `APPPATH/bootstrap.php` by adding the line 
     `'assets' => MODPATH.'assets'` to the call to `Kohana::modules()`.
  4. Create a directory `DOCROOT/assets/`, and make sure that PHP has permission
     to read/write to it.
  5. Make sure that files that exist (and are not in `APPPATH`, `MODPATH`, or 
     `SYSPATH` are served directly by Apache. IIRC this is default in the
     example .htaccess file packaged with Kohana 3.2.

Usage
-----

Put your assets in `APPPATH/assets/`, and in your views link to them like:

```html
<head>
  <title>Test</title>
  <?php echo HTML::style('assets/css/default.css'); ?>
</head>
```

What does it do?
----------------

Assuming default configuration, when an asset is requested, e.g. 
`assets/css/default.css`:

  - If the asset has already been compiled, it'll exist in the `DOCROOT/assets/`
    folder and be served directly by Apache.
  - Otherwise, the following are possible source files in `APPATH/assets/`:
      - A single file `css/default.(css|less)`.
      - All `\*.(css|less)` files directly under the directory `css/default/`.
  - The source files are compiled and written to `DOCROOT/assets/default.css`.

This is the same for JavaScript (.js) assets. Output is minified and, depending
on your Kohana configuration, gzipped.

For assets that do not need to be compiled (e.g. images), a symbolic link is
created to the source in `DOCROOT/assets/`.

Caveats
-------

Assets are compiled only once. If you've updated the souce files for a 
particular asset and want it to be recompiled, you must either:

  - Delete the compiled asset from `DOCROOT/assets/`, or
  - Enable the `check\_assets` option.

The `check\_assets` option causes the module to check for source modifications
for each asset in the `DOCROOT/assets/` directory every time it is initialized,
i.e. on every page served by Kohana, and recompile the asset if changes were
made.

Though it is not particularly expensive to leave `check\_assets` on, it is 
probably best practice to enable it only when in development.

