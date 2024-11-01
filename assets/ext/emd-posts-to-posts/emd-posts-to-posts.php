<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;
if ( ! defined( 'EMD_P2P_PLUGIN_VERSION' ) ){
	define( 'EMD_P2P_PLUGIN_VERSION', '1.4.3' );
}
require dirname( __FILE__ ) . '/scb/load.php';

if (!function_exists('emd_p2p_load_files')){
function emd_p2p_load_files( $dir, $files ) {
        foreach ( $files as $file )
                require_once "$dir/$file.php";
}
}

if (!function_exists('emd_p2p_load')){
function emd_p2p_load() {
	if ( function_exists( 'p2p_register_connection_type' ) ) return;
	$base = dirname( __FILE__ );


	emd_p2p_load_files( "$base/core", array(
		'storage', 'query', 'query-post', 'query-user', 'url-query',
		'util', 'item', 'list', 'side',
		'type-factory', 'type', 'directed-type', 'indeterminate-type',
		'api', 'extra'
	) );

	P2P_Widget::init();
	P2P_Shortcodes::init();

	if ( is_admin() ) {
		emd_p2p_load_files( "$base/admin", array(
			'mustache', 'factory',
			'box-factory', 'box', 'fields',
			'column-factory', 'column',
		) );
	}

	register_uninstall_hook( __FILE__, array( 'P2P_Storage', 'uninstall' ) );
}
}
emd_scb_init( 'emd_p2p_load' );

if (!function_exists('emd_p2p_init')){
function emd_p2p_init() {
	// Safe hook for calling p2p_register_connection_type()
	do_action( 'emd_p2p_init' );
}
}
add_action( 'wp_loaded', 'emd_p2p_init' );

