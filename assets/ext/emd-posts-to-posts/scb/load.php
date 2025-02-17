<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

$GLOBALS['emd_scb_data'] = array( 57, __FILE__, array(
	'EmdScbUtil', 'EmdScbOptions', 'EmdScbForms', 'EmdScbTable',
	'EmdScbWidget', 'EmdScbAdminPage', 'EmdScbBoxesPage',
	'EmdScbCron', 'EmdScbHooks',
) );

if ( !class_exists( 'EmdScbLoad4' ) ) :
/**
 * The main idea behind this class is to load the most recent version of the scb classes available.
 *
 * It waits until all plugins are loaded and then does some crazy hacks to make activation hooks work.
 */
class EmdScbLoad4 {

	private static $candidates = array();
	private static $classes;
	private static $callbacks = array();

	private static $loaded;

	static function init( $callback = '' ) {
		list( $rev, $file, $classes ) = $GLOBALS['emd_scb_data'];

		self::$candidates[$file] = $rev;
		self::$classes[$file] = $classes;

		if ( !empty( $callback ) ) {
			self::$callbacks[$file] = $callback;

			add_action( 'activate_plugin',  array( __CLASS__, 'delayed_activation' ) );
		}

		if ( did_action( 'plugins_loaded' ) )
			self::load();
		else
			add_action( 'plugins_loaded', array( __CLASS__, 'load' ), 9, 0 );
	}

	static function delayed_activation( $plugin ) {
		$plugin_dir = dirname( $plugin );

		if ( '.' == $plugin_dir )
			return;

		foreach ( self::$callbacks as $file => $callback ) {
			if ( dirname( dirname( plugin_basename( $file ) ) ) == $plugin_dir ) {
				self::load( false );
				call_user_func( $callback );
				do_action( 'emd_scb_activation_' . $plugin );
				break;
			}
		}
	}

	static function load( $do_callbacks = true ) {
		arsort( self::$candidates );

		$file = key( self::$candidates );

		$path = dirname( $file ) . '/';

		foreach ( self::$classes[$file] as $class_name ) {
			if ( class_exists( $class_name ) )
				continue;

			$fpath = $path . substr( $class_name, 6 ) . '.php';
			if ( file_exists( $fpath ) ) {
				include $fpath;
				self::$loaded[] = $fpath;
			}
		}

		if ( $do_callbacks )
			foreach ( self::$callbacks as $callback )
				call_user_func( $callback );
	}

	static function get_info() {
		arsort( self::$candidates );

		return array( self::$loaded, self::$candidates );
	}
}
endif;

if ( !function_exists( 'emd_scb_init' ) ) :
function emd_scb_init( $callback = '' ) {
	EmdScbLoad4::init( $callback );
}
endif;

