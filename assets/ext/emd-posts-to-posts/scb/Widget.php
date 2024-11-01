<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

// Adds compatibility methods between WP_Widget and EmdScbForms

abstract class EmdScbWidget extends WP_Widget {
	protected $defaults = array();

	private static $emd_scb_widgets = array();

	static function init( $class, $file = '', $base = '' ) {
		self::$emd_scb_widgets[] = $class;

		//remove this for now ,  it's not working
		//add_action( 'widgets_init', array( __CLASS__, 'emd_scb_register' ) );

		// for auto-uninstall
		if ( $file && $base && class_exists( 'EmdScbOptions' ) )
			new EmdScbOptions( "widget_$base", $file );
	}

	static function emd_scb_register() {
		foreach ( self::$emd_scb_widgets as $widget )
			register_widget( $widget );
	}

	// A pre-filled method, for convenience
	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );

		extract( $args );

		echo esc_html($before_widget);

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		if ( ! empty( $title ) )
			echo esc_html($before_title . $title . $after_title);

		$this->content( $instance );

		echo esc_html($after_widget);
	}

	// This is where the actual widget content goes
	function content( $instance ) {}


//_____HELPER METHODS_____


	// See EmdScbForms::input()
	// Allows extra parameter $args['title']
	protected function input( $args, $formdata = array() ) {
		$prefix = array( 'widget-' . $this->id_base, $this->number );

		$form = new EmdScbForm( $formdata, $prefix );

		// Add default class
		if ( !isset( $args['extra'] ) && 'text' == $args['type'] )
			$args['extra'] = array( 'class' => 'widefat' );

		// Add default label position
		if ( !in_array( $args['type'], array( 'checkbox', 'radio' ) ) && empty( $args['desc_pos'] ) )
			$args['desc_pos'] = 'before';

		$name = $args['name'];

		if ( !is_array( $name ) && '[]' == substr( $name, -2 ) )
			$name = array( substr( $name, 0, -2 ), '' );

		$args['name'] = $name;

		return $form->input( $args );
	}
}

