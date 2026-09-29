<?php

require __DIR__ . '/bootstrap.php';

$_POST = array(
	'action' => 'qrchk',
	'nonce' => 'test-nonce',
	'data' => 'https://sho.rt/abc',
);

require dirname( __DIR__ ) . '/assets/qrchk.php';

$path = iqrcodes_test_cache_path() . '/qrc_' . md5( 'https://sho.rt/abc' ) . '.png';
iqrcodes_test_assert( is_file( $path ) && filesize( $path ) > 0, 'Cache-miss endpoint did not generate a QR code.' );

echo "qrchk test passed\n";
