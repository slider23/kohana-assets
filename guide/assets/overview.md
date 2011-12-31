# Overview

When an asset is requested, e.g. `assets/css/default.css`:

  1. If the asset has previously been compiled, it will exist in `DOCROOT/assets/`
     and be served directly by the server.
  2. If it has not been compiled, it'll look for `APPATH/assets/css/default.(css|less)`
  3. If a source file is found, it's compiled and written to 
     `DOCROOT/assets/default.css`

Source file types can be added/removed in the config. Assets that have multiple source
files can also be specified in the config.
