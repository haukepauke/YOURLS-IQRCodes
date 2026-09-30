<?php

require __DIR__ . '/bootstrap.php';

$cache = iqrcodes_test_cache_path();
iqrcodes_mkdir( $cache );

iqrcodes_test_assert( iqrcodes_qr_format( 'png' ) === 'png', 'PNG format was not accepted.' );
iqrcodes_test_assert( iqrcodes_qr_format( 'jpeg' ) === 'jpeg', 'JPEG format was not accepted.' );
iqrcodes_test_assert( iqrcodes_qr_format( 'jpg' ) === 'jpeg', 'JPG alias was not normalized to JPEG.' );
iqrcodes_test_assert( iqrcodes_qr_format( 'svg' ) === 'svg', 'SVG format was not accepted.' );
iqrcodes_test_assert( iqrcodes_qr_format( 'gif' ) === 'png', 'Unsupported format did not use PNG default.' );
iqrcodes_test_assert( iqrcodes_qr_url( 'https://sho.rt/abc', 'jpeg', true ) === 'https://sho.rt/abc.qr?format=jpeg&download=1', 'QR download URL is invalid.' );

$shareBoxData = iqrcodes_sharebox( array( 'shorturl' => 'https://sho.rt/abc', 'shortlink_title' => '' ) );
$shareBox = $shareBoxData['shortlink_title'];
iqrcodes_test_assert( strpos( $shareBox, 'Download:' ) !== false, 'Existing short URL share box does not offer downloads.' );
iqrcodes_test_assert( strpos( $shareBox, 'format=png' ) !== false, 'Existing short URL share box does not use PNG for the preview.' );
foreach ( array( 'png', 'jpeg', 'svg' ) as $format ) {
	iqrcodes_test_assert( strpos( $shareBox, 'format=' . $format . '&download=1' ) !== false, strtoupper( $format ) . ' download is missing from existing short URL share box.' );
}

foreach ( array( 'png' => 'png', 'jpeg' => 'jpg', 'svg' => 'svg' ) as $format => $extension ) {
	$path = iqrcodes_generate_qr( 'https://sho.rt/abc', $format, iqrcodes_get_opts() );
	iqrcodes_test_assert( $path === $cache . '/qrc_' . md5( 'https://sho.rt/abc' ) . '.' . $extension, strtoupper( $format ) . ' cache filename is invalid.' );
	iqrcodes_test_assert( is_file( $path ) && filesize( $path ) > 0, strtoupper( $format ) . ' QR code was not created.' );
	copy( $path, $cache . '/without-logo.' . $extension );
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
