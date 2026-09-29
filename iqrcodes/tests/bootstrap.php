<?php

define( 'YOURLS_ABSPATH', __DIR__ . '/yourls/' );
define( 'YOURLS_SITE', 'https://sho.rt' );

$GLOBALS['iqrcodes_test_root'] = sys_get_temp_dir() . '/iqrcodes-' . bin2hex( random_bytes( 8 ) );
$GLOBALS['iqrcodes_test_options'] = array(
	'iqrcodes_usrv_dir' => 'qr',
	'iqrcodes_EC' => 'H',
	'iqrcodes_img_size' => '5',
	'iqrcodes_border_size' => '2',
	'iqrcodes_afterlife' => 'preserve',
	'iqrcodes_imagetype' => 'png',
	'iqrcodes_logo_scale' => '0.25',
	'iqrcodes_logo_position' => 'center',
	'iqrcodes_logo_file_type' => 'png',
	'iqrcodes_logo_do' => 'no',
	'usrv_cache_loc' => $GLOBALS['iqrcodes_test_root'],
);
mkdir( $GLOBALS['iqrcodes_test_root'], 0700, true );

class IQRCodesTestRedirect extends RuntimeException {}

function yourls_add_action( $hook, $callback ) {}
function yourls_add_filter( $hook, $callback ) {}
function yourls_get_option( $name ) { return $GLOBALS['iqrcodes_test_options'][$name] ?? null; }
function yourls_update_option( $name, $value ) { $GLOBALS['iqrcodes_test_options'][$name] = $value; }
function yourls_delete_option( $name ) { unset( $GLOBALS['iqrcodes_test_options'][$name] ); }
function yourls_create_nonce( $action ) { return 'test-nonce'; }
function yourls_verify_nonce( $action, $nonce = false ) { iqrcodes_test_assert( $action === 'iqrcodes-qrchk' && $nonce === 'test-nonce', 'Unexpected nonce.' ); }
function yourls_sanitize_url( $url ) { return $url; }
function yourls_sanitize_keyword( $keyword ) { return preg_replace( '/[^a-z0-9]/', '', $keyword ); }
function yourls_is_shorturl( $keyword ) { return $keyword === 'abc'; }
function yourls_make_regexp_pattern( $charset ) { return $charset; }
function yourls_get_shorturl_charset() { return 'a-z0-9'; }
function yourls_redirect( $url ) { throw new IQRCodesTestRedirect( $url ); }
function yourls_is_API() { return false; }
function yourls_is_active_plugin( $plugin ) { return true; }
function yourls_register_plugin_page( $slug, $title, $callback ) {}

function iqrcodes_test_assert( $condition, $message ) {
	if ( !$condition ) {
		throw new RuntimeException( $message );
	}
}

function iqrcodes_test_cache_path() {
	return $GLOBALS['iqrcodes_test_root'] . '/qr';
}

require_once dirname( __DIR__ ) . '/plugin.php';
