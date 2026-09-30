<?php

require __DIR__ . '/bootstrap.php';

$original = array(
	'shorturl' => 'https://sho.rt/abc',
	'shortlink_title' => '<h3>Short link</h3>',
	'share_title' => '<h3>Quick Share</h3>',
);
$rendered = iqrcodes_sharebox( $original );
iqrcodes_test_assert( $rendered['shortlink_title'] === $original['shortlink_title'], 'QR panel altered the short-link box.' );

$document = new DOMDocument();
$document->loadHTML( '<div id="sharebox">' . $rendered['share_title'] . '</div>' );
$xpath = new DOMXPath( $document );
$panels = $xpath->query( '//*[@id="sharebox"]/*[@id="qrcode"]' );
iqrcodes_test_assert( $panels->length === 1, 'QR panel is not inside the Share box.' );

$images = $xpath->query( './/img[@id="iqrcodes-image"]', $panels->item( 0 ) );
iqrcodes_test_assert( $images->length === 1, 'QR preview is missing.' );
iqrcodes_test_assert( $images->item( 0 )->getAttribute( 'src' ) === 'https://sho.rt/abc.qr?format=png', 'QR preview must use PNG.' );

$links = $xpath->query( './/a', $panels->item( 0 ) );
iqrcodes_test_assert( $links->length === 3, 'Expected three QR download links.' );
foreach ( array( 'png', 'jpeg', 'svg' ) as $index => $format ) {
	$link = $links->item( $index );
	iqrcodes_test_assert( strtolower( $link->textContent ) === $format, strtoupper( $format ) . ' download label is missing.' );
	iqrcodes_test_assert( $link->getAttribute( 'href' ) === 'https://sho.rt/abc.qr?format=' . $format . '&download=1', strtoupper( $format ) . ' download URL is incorrect.' );
	iqrcodes_test_assert( $link->getAttribute( 'target' ) === '_blank', 'Download should leave the Share page open.' );
}

$empty = array( 'shorturl' => '', 'shortlink_title' => '<h2>Your short link</h2>', 'share_title' => '<h2>Quick Share</h2>' );
iqrcodes_test_assert( iqrcodes_sharebox( $empty ) === $empty, 'The empty list-page share box was modified.' );

echo "share box test passed\n";
