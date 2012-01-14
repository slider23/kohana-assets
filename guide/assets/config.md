# Config

Type      | Option           | Description                | Default value
----------|------------------|----------------------------| -------------------------
`string`  | sources          | Source directory           | `APPPATH/assets`
`string`  | targets          | Target directory           | `DOCROOT/assets`
`boolean` | watch            | Check for source file changes and recompile as necessary | `Kohana::$environment === Kohana::DEVELOPMENT`
`array`   | compile\_folders | Source folders that can be compiled into a single asset (see [usage](usage)) | `array()`
`array`   | types            | Source types that require a compilation step and their associated file extension(s) | See [extending](extending)
`array`   | target\_types    | Target types and their associated source types | See [extending](extending)
