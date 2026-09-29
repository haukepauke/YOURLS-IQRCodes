<?php

require __DIR__ . '/bootstrap.php';

$cache = iqrcodes_test_cache_path();
iqrcodes_mkdir( $cache );

foreach ( array( 'png', 'jpg', 'svg' ) as $format ) {
	$path = $cache . '/without-logo.' . $format;
	QRcode::{$format}( 'https://sho.rt/abc', $path, 'H', 5, 2 );
	iqrcodes_test_assert( is_file( $path ) && filesize( $path ) > 0, strtoupper( $format ) . ' QR code was not created.' );
}

iqrcodes_test_assert( getimagesize( $cache . '/without-logo.png' )[2] === IMAGETYPE_PNG, 'PNG output is invalid.' );
iqrcodes_test_assert( getimagesize( $cache . '/without-logo.jpg' )[2] === IMAGETYPE_JPEG, 'JPEG output is invalid.' );
iqrcodes_test_assert( strpos( file_get_contents( $cache . '/without-logo.svg' ), '<svg' ) !== false, 'SVG output is invalid.' );

$logo = imagecreatetruecolor( 20, 10 );
$red = imagecolorallocate( $logo, 255, 0, 0 );
imagefill( $logo, 0, 0, $red );
imagepng( $logo, $cache . '/logo.png' );
$GLOBALS['iqrcodes_test_options']['iqrcodes_logo_do'] = 'yes';

QRcode::png( 'https://sho.rt/abc', $cache . '/with-logo.png', 'H', 5, 2 );
QRcode::svg( 'https://sho.rt/abc', $cache . '/with-logo.svg', 'H', 5, 2 );
iqrcodes_test_assert( hash_file( 'sha256', $cache . '/without-logo.png' ) !== hash_file( 'sha256', $cache . '/with-logo.png' ), 'PNG logo was not applied.' );
iqrcodes_test_assert( strpos( file_get_contents( $cache . '/with-logo.svg' ), 'data:image/png;base64,' ) !== false, 'SVG logo was not embedded.' );

echo "generation tests passed\n";
