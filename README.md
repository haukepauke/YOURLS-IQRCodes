# YOURLS-IQRCodes
YOURLS Integrated QRCodes plugin with exposed options and full integration

This is an updated fork of [Inline QRCode](http://techlister.com/plugins-2/qrcode-plugin-for-yourls/354/) which is more compact, configurable, and just as efficient with more features.

## Requirements

- YOURLS 1.9 or later
- PHP 8.2 or later
- PHP extensions: `gd`, `mbstring`, `dom`, and `fileinfo`
- Composer 2 on a build or target host (see the no-Composer deployment option below)

## Features

- Generates and caches QR codes for new, edited, existing, and requested short URLs.
- Serves PNG, JPEG, or SVG from a stable URL: append `.qr` to a short URL, for
  example `https://sho.rt/keyword.qr`. Choose a format with `?format=png`,
  `?format=jpeg`, or `?format=svg`.
- Generates codes locally with `chillerlan/php-qrcode`; no external QR-code
  service or YOURLS-U-SRV plugin is required.
- Shows a PNG QR-code preview and PNG, JPEG, and SVG download links in the
  Share box for both existing and newly created short URLs.
- Provides admin settings for size, border width, error-correction level, image
  format, logo watermark, and cache cleanup when deactivating the plugin.
- Can scan the database and generate missing QR-code cache files in bulk.

## Install with YOURLS

The following assumes a YOURLS installation at `/var/www/yourls`. Clone the
repository outside the public plugin directory, then symlink its `iqrcodes`
directory into YOURLS:

```sh
git clone https://github.com/haukepauke/YOURLS-IQRCodes.git /var/www/yourls/.yourls-iqrcodes
composer --working-dir=/var/www/yourls/.yourls-iqrcodes/iqrcodes \
  install --no-dev --prefer-dist --optimize-autoloader
ln -s ../../.yourls-iqrcodes/iqrcodes /var/www/yourls/user/plugins/iqrcodes
```

`composer.lock` pins the exact dependency versions. Use `composer install`, not
`composer update`, when deploying.

Ensure the PHP-FPM/Apache user can create and write the private cache directory:

```sh
install -d -o www-data -g www-data -m 0750 /var/www/YOURLS_CACHE/iqrcodes
```

If your cache belongs elsewhere, set `IQRCODES_CACHE_DIR` in `user/config.php`:

```php
define('IQRCODES_CACHE_DIR', '/srv/yourls-cache/iqrcodes');
```

Finally, enable **IQRCodes** in the YOURLS Plugins admin page. QR images are
available at `https://sho.rt/keyword.qr`; no U-SRV plugin or extra YOURLS page is
required.

## Deploy when Composer is unavailable on the target

Run Composer on a trusted build host with PHP 8.2 or later and the required
extensions. Package the plugin directory *including* the generated `vendor/`
directory, then copy that artifact to the target:

```sh
git clone https://github.com/haukepauke/YOURLS-IQRCodes.git build/YOURLS-IQRCodes
composer --working-dir=build/YOURLS-IQRCodes/iqrcodes \
  install --no-dev --prefer-dist --optimize-autoloader
tar -C build/YOURLS-IQRCodes -czf iqrcodes-plugin.tar.gz iqrcodes
```

Extract `iqrcodes-plugin.tar.gz` alongside the YOURLS installation and create
the same `user/plugins/iqrcodes` symlink. Do not commit `vendor/` to this
repository; it is a deployment artifact and is intentionally ignored by Git.

## Web-server routing

The `.qr` endpoint is handled by YOURLS, rather than a static file. Keep the
standard YOURLS rewrite configuration so requests for paths that do not exist
on disk reach `yourls-loader.php`. No separate `srv` endpoint or `qrchk.php`
route is needed.

To embed a QR code, use the short URL with `.qr` appended. The configured
default is used when no format is supplied; request any supported format with
the `format` parameter:

```html
<img src="https://sho.rt/keyword.qr" alt="QR code for https://sho.rt/keyword">
<img src="https://sho.rt/keyword.qr?format=svg" alt="SVG QR code for https://sho.rt/keyword">
```

Append `&download=1` to request a download, for example
`https://sho.rt/keyword.qr?format=jpeg&download=1`. The Share box always
previews PNG and offers direct download links for all three formats.

## Credits

[Inline QRcode](http://techlister.com/plugins-2/qrcode-plugin-for-yourls/354/) by Savoul Pelister is the base of this fork.

[chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) generates the QR codes.

[YOURLS](https://yourls.org/) provides the URL-shortening platform.

===========================

    Copyright (C) 2016 Josh Panter

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
