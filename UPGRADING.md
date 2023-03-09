# Upgrading

Because there are many breaking changes an upgrade is not that easy. There are many edge cases this guide does not
cover. We accept PRs to improve this guide.

## From v1.0 to v1.2

Example:

- The `crowdin-exporter.php` config was changed:
    - **Optional**. Please append new `source_language` key in this way:
      ```php
      'source_language' => 'en',
      ```

_Or you can delete your current config and publish it again (copy your changes before)._
