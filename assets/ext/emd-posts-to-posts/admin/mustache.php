<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

/**
 * @internal
 */
abstract class EMD_P2P_Mustache {

	private static $loader;
	private static $mustache;

	public static function init() {
		if ( !class_exists( 'Emd_Mustache' ) )
			require dirname(__FILE__) . '/../mustache/Mustache.php';

		if ( !class_exists( 'Emd_MustacheLoader' ) )
			require dirname(__FILE__) . '/../mustache/MustacheLoader.php';

		self::$loader = new Emd_MustacheLoader( dirname(__FILE__) . '/templates', 'html' );

		self::$mustache = new Emd_Mustache( null, null, self::$loader );
	}

	public static function render( $template, $data ) {
		return self::$mustache->render( self::$loader[$template], $data );
	}
}

EMD_P2P_Mustache::init();

