<?php

require __DIR__ . '/bootstrap.php';

iqrcodes_test_assert( iqrcodes_qr_keyword_from_request( 'abc.qr' ) === 'abc', '.qr route did not recognize a YOURLS request string.' );
iqrcodes_test_assert( iqrcodes_qr_keyword_from_request( 'abc.qr?format=svg' ) === 'abc', '.qr route did not ignore query parameters.' );
iqrcodes_test_assert( iqrcodes_qr_keyword_from_request( array( 'abc.qr' ) ) === 'abc', '.qr route did not support the legacy request array.' );
iqrcodes_test_assert( iqrcodes_qr_keyword_from_request( 'abc.q' ) === false, '.q must not be accepted as a QR route.' );

echo "qr route test passed\n";
