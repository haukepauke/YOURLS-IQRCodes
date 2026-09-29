<?php

/*
 * Compatibility adapter for the legacy IQRCodes QRcode::png/jpg/svg API.
 *
 * The QR matrix and output generation are provided by chillerlan/php-qrcode.
 * Run `composer install` from the iqrcodes directory before enabling the plugin.
 */

$iqrcodesAutoloader = dirname( __DIR__ ) . '/vendor/autoload.php';
if ( !is_file( $iqrcodesAutoloader ) ) {
	throw new RuntimeException( 'IQRCodes requires its Composer dependencies. Run composer install in user/plugins/iqrcodes.' );
}

require_once $iqrcodesAutoloader;

final class QRcode {
	public static function png( $text, $outfile = false, $level = 'L', $size = 3, $margin = 4 ) {
		self::render( $text, $outfile, $level, $size, $margin, \chillerlan\QRCode\Output\QRGdImagePNG::class, 'png' );
	}

	public static function jpg( $text, $outfile = false, $level = 'L', $size = 3, $margin = 4 ) {
		self::render( $text, $outfile, $level, $size, $margin, \chillerlan\QRCode\Output\QRGdImageJPEG::class, 'jpg' );
	}

	public static function svg( $text, $outfile = false, $level = 'L', $size = 3, $margin = 4 ) {
		self::render( $text, $outfile, $level, $size, $margin, \chillerlan\QRCode\Output\QRMarkupSVG::class, 'svg' );
	}

	private static function render( $text, $outfile, $level, $size, $margin, $outputInterface, $format ) {
		if ( !is_string( $text ) || !is_string( $outfile ) || $outfile === '' ) {
			throw new InvalidArgumentException( 'QR code text and output filename are required.' );
		}

		$options = new \chillerlan\QRCode\QROptions( array(
			'eccLevel' => strtoupper( (string) $level ),
			'scale' => max( 1, min( 100, (int) $size ) ),
			'addQuietzone' => true,
			'quietzoneSize' => max( 0, min( 10, (int) $margin ) ),
			'outputBase64' => false,
			'outputInterface' => $outputInterface,
		) );

		(new \chillerlan\QRCode\QRCode( $options ))->render( $text, $outfile );
		self::addLogo( $outfile, $format );
	}

	private static function addLogo( $outfile, $format ) {
		if ( !function_exists( 'iqrcodes_get_opts' ) ) {
			return;
		}

		$opt = iqrcodes_get_opts();
		if ( $opt[9] !== 'yes' || !in_array( $opt[8], array( 'jpg', 'png' ), true ) ) {
			return;
		}

		$logoPath = $opt[10] . '/logo.' . $opt[8];
		if ( !is_file( $logoPath ) || !is_readable( $logoPath ) ) {
			return;
		}

		if ( $format === 'svg' ) {
			self::addLogoToSvg( $outfile, $logoPath, $opt );
			return;
		}

		self::addLogoToRasterImage( $outfile, $logoPath, $format, $opt );
	}

	private static function addLogoToRasterImage( $outfile, $logoPath, $format, $opt ) {
		$createQrImage = $format === 'png' ? 'imagecreatefrompng' : 'imagecreatefromjpeg';
		$writeQrImage = $format === 'png' ? 'imagepng' : 'imagejpeg';
		$createLogo = $opt[8] === 'png' ? 'imagecreatefrompng' : 'imagecreatefromjpeg';
		$qrImage = $createQrImage( $outfile );
		$logoImage = $createLogo( $logoPath );

		if ( $qrImage === false || $logoImage === false ) {
			return;
		}

		$position = self::logoPosition(
			imagesx( $qrImage ),
			imagesy( $qrImage ),
			imagesx( $logoImage ),
			imagesy( $logoImage ),
			$opt
		);
		imagecopyresampled(
			$qrImage,
			$logoImage,
			$position['x'],
			$position['y'],
			0,
			0,
			$position['width'],
			$position['height'],
			imagesx( $logoImage ),
			imagesy( $logoImage )
		);

		if ( $format === 'png' ) {
			$writeQrImage( $qrImage, $outfile );
		} else {
			$writeQrImage( $qrImage, $outfile, 90 );
		}
	}

	private static function addLogoToSvg( $outfile, $logoPath, $opt ) {
		$svg = new DOMDocument();
		$svg->preserveWhiteSpace = false;
		if ( @$svg->load( $outfile, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING ) === false ) {
			return;
		}

		$root = $svg->documentElement;
		$viewBox = preg_split( '/\s+/', trim( $root->getAttribute( 'viewBox' ) ) );
		$imageInfo = getimagesize( $logoPath );
		if ( count( $viewBox ) !== 4 || $imageInfo === false ) {
			return;
		}

		$position = self::logoPosition( (int) $viewBox[2], (int) $viewBox[3], $imageInfo[0], $imageInfo[1], $opt );
		$mimeType = $opt[8] === 'png' ? 'image/png' : 'image/jpeg';
		$image = $svg->createElementNS( 'http://www.w3.org/2000/svg', 'image' );
		$image->setAttribute( 'href', 'data:' . $mimeType . ';base64,' . base64_encode( file_get_contents( $logoPath ) ) );
		$image->setAttribute( 'x', (string) $position['x'] );
		$image->setAttribute( 'y', (string) $position['y'] );
		$image->setAttribute( 'width', (string) $position['width'] );
		$image->setAttribute( 'height', (string) $position['height'] );
		$root->appendChild( $image );
		$svg->save( $outfile );
	}

	private static function logoPosition( $qrWidth, $qrHeight, $logoWidth, $logoHeight, $opt ) {
		$scale = max( 0.05, min( 0.4, (float) $opt[6] ) );
		$targetWidth = max( 1, (int) round( $qrWidth * $scale ) );
		$targetHeight = max( 1, (int) round( $logoHeight * ( $targetWidth / $logoWidth ) ) );
		$x = (int) round( ( $qrWidth - $targetWidth ) / 2 );
		$y = (int) round( ( $qrHeight - $targetHeight ) / 2 );

		if ( $opt[7] === 'topleft' ) {
			$x = 0;
			$y = 0;
		} elseif ( $opt[7] === 'topright' ) {
			$x = $qrWidth - $targetWidth;
			$y = 0;
		}

		return array( 'x' => $x, 'y' => $y, 'width' => $targetWidth, 'height' => $targetHeight );
	}
}
