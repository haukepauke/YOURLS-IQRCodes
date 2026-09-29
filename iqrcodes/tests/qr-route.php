<?php

require __DIR__ . '/bootstrap.php';

try {
	iqrcode_dot_qr( array( 'abc.qr' ) );
} catch ( IQRCodesTestRedirect $redirect ) {
	$GLOBALS['iqrcodes_test_redirect'] = $redirect->getMessage();
}

$expectedFile = 'qrc_' . md5( 'https://sho.rt/abc' ) . '.png';
iqrcodes_test_assert( is_file( iqrcodes_test_cache_path() . '/' . $expectedFile ), '.qr route did not generate a QR code.' );
iqrcodes_test_assert( strpos( $GLOBALS['iqrcodes_test_redirect'] ?? '', 'fn=' . $expectedFile ) !== false, '.qr route did not redirect to the generated QR code.' );

echo "qr route test passed\n";
