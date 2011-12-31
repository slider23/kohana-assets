# Install

Besides putting the module in `MODPATH` and enabling it in `bootstrap.php,` 
you should:

  1. Create `APPPATH/assets/`
  2. Create `DOCROOT/assets/`, and make sure PHP has permission to write to it
  3. Make sure that in your server configuration files that exist are served 
     directly (e.g. `RewriteCond %{REQUEST_FILENAME} !-f` before rewriting to 
     index.php for Apache servers).
  3. Copy the config file to your application
