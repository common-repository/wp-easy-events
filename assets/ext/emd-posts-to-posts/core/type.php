<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class P2P_Connection_Type {

	protected $directed_class = 'P2P_Directed_Connection_Type';

	protected $arrow = '&rarr;';

	public $side;

	public $cardinality;

	public $labels;

	protected $title;

	public function __construct( $args, $sides ) {
		$this->side = $sides;

		$this->set_self_connections( $args );

		$this->set_cardinality( emd_p2p_pluck( $args, 'cardinality' ) );

		$labels = array();
		foreach ( array( 'from', 'to' ) as $key ) {
			$labels[ $key ] = (array) emd_p2p_pluck( $args, $key . '_labels' );
		}

		$this->labels = $labels;

		$this->fields = $this->expand_fields( emd_p2p_pluck( $args, 'fields' ) );

		foreach ( $args as $key => $value ) {
			$this->$key = $value;
		}
	}

	public function get_field( $field, $direction ) {
		$value = $this->$field;

		if ( 'title' == $field )
			return $this->expand_title( $value, $direction );

		if ( 'labels' == $field )
			return $this->expand_labels( $value, $direction );

		if ( false === $direction )
			return $value;

		return $value[ $direction ];
	}

	function _directions_for_admin( $direction, $show_ui ) {
		return array_intersect(
			emd_p2p_expand_direction( $show_ui ),
			emd_p2p_expand_direction( $direction )
		);
	}

	private function set_self_connections( &$args ) {
		$from_side = $this->side['from'];
		$to_side = $this->side['to'];

		if ( !$from_side->is_same_type( $to_side ) ) {
			$args['self_connections'] = true;
		}
	}

	private function expand_fields( $fields ) {
		foreach ( $fields as &$field_args )
		{
			if ( !is_array( $field_args ) )
				$field_args = array( 'title' => $field_args );

			if ( !isset( $field_args['type'] ) )
			{
				$field_args['type'] = isset( $field_args['values'] ) ? 'select' : 'text';
			}
			elseif ( 'checkbox' == $field_args['type'] && !isset( $field_args['values'] ) )
			{
				$field_args['values'] = array( true => ' ' );
			}
		}

		return $fields;
	}

	private function set_cardinality( $cardinality ) {
		$parts = explode( '-', $cardinality );

		$this->cardinality['from'] = $parts[0];
		$this->cardinality['to'] = $parts[2];

		foreach ( $this->cardinality as $key => &$value ) {
			if ( 'one' != $value )
				$value = 'many';
		}
	}

	private function expand_labels( $additional_labels, $key ) {
		$additional_labels['create'] = __( 'Create connections', 'wp-easy-events' );

		$labels = clone $this->side[ $key ]->get_labels();

		foreach ( $additional_labels as $key => $var )
			$labels->$key = $var;

		return $labels;
	}

	private function expand_title( $title, $key ) {
		if ( $title && !is_array( $title ) )
			return $title;

		if ( isset( $title[$key] ) )
			return $title[$key];

		$other_key = ( 'from' == $key ) ? 'to' : 'from';

		return sprintf(
			__( 'Connected %s', 'wp-easy-events' ),
			$this->side[ $other_key ]->get_title()
		);
	}

	public function __call( $method, $args ) {
		if ( ! method_exists( 'P2P_Directed_Connection_Type', $method ) ) {
			trigger_error( "Method '$method' does not exist.", E_USER_ERROR );
			return;
		}

		// TODO: make find_direction() return the normalized item and pass that along
		$directed = $this->find_direction( $args[0] );
		if ( !$directed ) {
			trigger_error( sprintf( "Can't determine direction for '%s' type.", $this->name ), E_USER_WARNING );
			return false;
		}

		return call_user_func_array( array( $directed, $method ), $args );
	}

	/**
	 * Set the direction.
	 *
	 * @param string $direction Can be 'from', 'to' or 'any'.
	 *
	 * @return object P2P_Directed_Connection_Type instance
	 */
	public function set_direction( $direction, $instantiate = true ) {
		if ( !in_array( $direction, array( 'from', 'to', 'any' ) ) )
			return false;

		if ( $instantiate ) {
			$class = $this->directed_class;

			return new $class( $this, $direction );
		}

		return $direction;
	}

	/**
	 * Attempt to guess direction based on a parameter.
	 *
	 * @param mixed A post type, object or object id.
	 * @param bool Whether to return an instance of P2P_Directed_Connection_Type or just the direction
	 * @param string An object type, such as 'post' or 'user'
	 *
	 * @return bool|object|string False on failure, P2P_Directed_Connection_Type instance or direction on success.
	 */
	public function find_direction( $arg, $instantiate = true, $object_type = null ) {
		if ( is_array( $arg ) )
			$arg = reset( $arg );

		if ( $object_type ) {
			$direction = $this->direction_from_object_type( $object_type );
			if ( !$direction )
				return false;

			if ( in_array( $direction, array( 'from', 'to' ) ) )
				return $this->set_direction( $direction, $instantiate );
		}

		$direction = $this->direction_from_item( $arg );

		if ( $direction )
			return $this->set_direction( $direction, $instantiate );

		return false;
	}

	protected function choose_direction( $direction ) {
		return $direction;
	}

	protected function direction_from_item( $arg ) {
		foreach ( array( 'from', 'to' ) as $direction ) {
			$item = $this->side[ $direction ]->item_recognize( $arg );

			if ( !$item )
				continue;

			return $this->choose_direction( $direction );
		}

		return false;
	}

	protected function direction_from_object_type( $current ) {
		$from = $this->side['from']->get_object_type();
		$to = $this->side['to']->get_object_type();

		if ( $from == $to && $current == $from )
			return 'any';

		if ( $current == $from )
			return 'to';

		if ( $current == $to )
			return 'from';

		return false;
	}

	public function direction_from_types( $object_type, $post_types = null ) {
		foreach ( array( 'from', 'to' ) as $direction ) {
			if ( !$this->_type_check( $direction, $object_type, $post_types ) )
				continue;

			return $this->choose_direction( $direction );
		}

		return false;
	}

	private function _type_check( $direction, $object_type, $post_types ) {
		if ( $object_type != $this->side[ $direction ]->get_object_type() )
			return false;

		$side = $this->side[ $direction ];

		if ( !method_exists( $side, 'recognize_post_type' ) )
			return true;

		foreach ( (array) $post_types as $post_type ) {
			if ( $side->recognize_post_type( $post_type ) ) {
				return true;
			}
		}

		return false;
	}

	/** Alias for get_prev() */
	public function get_previous( $from, $to ) {
		return $this->get_prev( $from, $to );
	}

	/**
	 * Get the previous post in an ordered connection.
	 *
	 * @param int The first end of the connection.
	 * @param int The second end of the connection.
	 *
	 * @return bool|object False on failure, post object on success
	 */
	public function get_prev( $from, $to ) {
		return $this->get_adjacent( $from, $to, -1 );
	}

	/**
	 * Get the next post in an ordered connection.
	 *
	 * @param int The first end of the connection.
	 * @param int The second end of the connection.
	 *
	 * @return bool|object False on failure, post object on success
	 */
	public function get_next( $from, $to ) {
		return $this->get_adjacent( $from, $to, +1 );
	}

	/**
	 * Get another post in an ordered connection.
	 *
	 * @param int The first end of the connection.
	 * @param int The second end of the connection.
	 * @param int The position relative to the first parameter
	 *
	 * @return bool|object False on failure, post object on success
	 */
	public function get_adjacent( $from, $to, $which ) {

		// The direction needs to be based on the second parameter,
		// so that it's consistent with $this->connect( $from, $to ) etc.
		$directed = $this->find_direction( $to );
		if ( !$directed )
			return false;

		$key = $directed->get_orderby_key();
		if ( !$key )
			return false;

		$p2p_id = $directed->get_p2p_id( $to, $from );
		if ( !$p2p_id )
			return false;

		$order = (int) emd_p2p_get_meta( $p2p_id, $key, true );

		$adjacent = $directed->get_connected( $to, array(
			'connected_meta' => array(
				array(
					'key' => $directed->get_orderby_key(),
					'value' => $order + $which
				)
			)
		), 'abstract' );

		return emd_p2p_first( $adjacent->items );
	}

	/**
	 * Optimized inner query, after the outer query was executed.
	 *
	 * Populates each of the outer querie's $post objects with a 'connected' property, containing a list of connected posts
	 *
	 * @param object|array $items WP_Query instance or list of post objects
	 * @param string|array $extra_qv Additional query vars for the inner query.
	 * @param string $prop_name The name of the property used to store the list of connected items on each post object.
	 */
	public function each_connected( $items, $extra_qv = array(), $prop_name = 'connected' ) {
		if ( is_a( $items, 'WP_Query' ) )
			$items =& $items->posts;

		if ( empty( $items ) || !is_object( $items[0] ) )
			return;

		$post_types = array_unique( wp_list_pluck( $items, 'post_type' ) );

		if ( count( $post_types ) > 1 ) {
			$extra_qv['post_type'] = 'any';
		}

		$possible_directions = array();

		foreach ( array( 'from', 'to' ) as $direction ) {
			$side = $this->side[ $direction ];

			if ( 'post' == $side->get_object_type() ) {
				foreach ( $post_types as $post_type ) {
					if ( $side->recognize_post_type( $post_type ) ) {
						$possible_directions[] = $direction;
					}
				}
			}
		}

		$direction = emd_p2p_compress_direction( $possible_directions );

		if ( !$direction )
			return false;

		$directed = $this->set_direction( $direction );

		// ignore pagination
		foreach ( array( 'showposts', 'posts_per_page', 'posts_per_archive_page' ) as $disabled_qv ) {
			if ( isset( $extra_qv[ $disabled_qv ] ) ) {
				trigger_error( "Can't use '$disabled_qv' in an inner query", E_USER_WARNING );
			}
		}
		$extra_qv['nopaging'] = true;

		$q = $directed->get_connected( $items, $extra_qv, 'abstract' );

		$raw_connected = array();
		foreach ( $q->items as $item )
			$raw_connected[] = $item->get_object();

		emd_p2p_distribute_connected( $items, $raw_connected, $prop_name );
	}

	public function get_desc() {
		foreach ( array( 'from', 'to' ) as $key ) {
			$$key = $this->side[ $key ]->get_desc();
		}

		$label = "$from {$this->arrow} $to";

		$title = $this->get_field( 'title', 'from' );

		if ( $title )
			$label .= " ($title)";

		return $label;
	}
}


class P2P_Indeterminate_Connection_Type extends P2P_Connection_Type {

	protected $directed_class = 'P2P_Indeterminate_Directed_Connection_Type';

	protected $arrow = '&harr;';

	protected function choose_direction( $direction ) {
		return 'from';
	}

	function _directions_for_admin( $direction, $show_ui ) {
		return parent::_directions_for_admin( 'any', $show_ui );
	}
}


class P2P_Reciprocal_Connection_Type extends P2P_Indeterminate_Connection_Type {

	protected function choose_direction( $direction ) {
		return 'any';
	}

	function _directions_for_admin( $direction, $show_ui ) {
		if ( $show_ui )
			$directions = array( 'any' );
		else
			$directions = array();

		return $directions;
	}
}

