# Configuration

Type      | Option           | Description                | Default value
----------|------------------|----------------------------| -------------------------
`string`  | source\_dir      | Source directory (relative)| `assets/`
`string`  | target\_dir      | Target directory           | `DOCROOT/assets/`
`array`   | concatable       | Source folders that can be compiled into a single asset (see [usage](usage)) | `array()`
`array`   | types            | Source types that require a compilation step and their associated file extension(s) | See [extending](extending)
`array`   | target\_types    | Target types and their associated source types | See [extending](extending)
`boolean` | watch            | Check for source file changes and recompile as necessary | `Kohana::$environment === Kohana::DEVELOPMENT`

