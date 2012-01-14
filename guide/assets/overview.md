# Overview

**kohana-assets** comes out-of-the-box with support for CSS, LESS, JavaScript,
and CoffeeScript (eventually). It can be [extended](extending) to add or change
the compilation step for these and other types of assets.

## Request Flow

  1. If the asset has already been compiled, it will exist in the `targets` 
    (see [configuration](config))  directory and be served directly by Apache.
  2. If the asset is one of `target_types`, search for its source file(s) in the
     `sources` directory, compile, save, and redirect to the compiled asset (1).
  3. If the asset is one of `types`, the client is trying to access a
     source file directly, which is not allowed. Otherwise, simply link to
     the asset if it exists (no compilation step).
