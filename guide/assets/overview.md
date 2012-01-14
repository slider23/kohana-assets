# Overview

**kohana-assets** comes out-of-the-box with support for CSS, LESS, JavaScript, and
CoffeeScript (eventually). It can be extended to add or change the compilation
step for these and other types of assets.

When an asset is requested:

  1. If the asset has already been compiled, it will exist in the `assets.targets`
     directory and be served directly by Apache.
  2. If the asset is one of `assets.target_types`, search for its source file(s) in 
     `assets.sources`, compile, save, and redirect to the compiled asset (1).
  3. If the asset is one of `assets.types`, the client is trying to access a
     source file directly, which is not allowed (404). Otherwise, simply link to
     the asset if it exists (no compilation step).
