<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

interface P2P_Field {
	function get_title();
	function render( $p2p_id, $item );
}

class P2P_Box {
	private $ctype;

	private $args;

	private $columns;

	private static $enqueued_scripts = false;

	private static $admin_box_qv = array(
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'post_status' => 'any',
	);

	function __construct( $args, $columns, $ctype ) {
		$this->args = $args;

		$this->columns = $columns;

		$this->ctype = $ctype;

		$this->labels = $this->ctype->get( 'opposite', 'labels' );
	}

	public function init_scripts() {

		if ( self::$enqueued_scripts )
			return;

		wp_enqueue_style( 'p2p-box', plugins_url( 'box.css', __FILE__ ), array(), EMD_P2P_PLUGIN_VERSION );

		wp_enqueue_script( 'p2p-box', plugins_url( 'box.js', __FILE__ ), array( 'jquery' ), EMD_P2P_PLUGIN_VERSION, true );
		
		$add_js_func = Array();
		$add_js_func = apply_filters('emd_ext_p2p_add_js_func',$add_js_func);
		$add_js_fname = '';
		$add_js_p2p_type = '';
		if(!empty($add_js_func) && !empty($add_js_func['fname'])){
			$add_js_fname = $add_js_func['fname'];
		}
		if(!empty($add_js_func) && !empty($add_js_func['p2p_type'])){
			$add_js_p2p_type = $add_js_func['p2p_type'];
		}
		wp_localize_script( 'p2p-box', 'P2PAdmin', array(
			'nonce' => wp_create_nonce( P2P_BOX_NONCE ),
			'spinner' => admin_url( 'images/wpspin_light.gif' ),
			'deleteConfirmMessage' => __( 'Are you sure you want to delete all connections?', 'wp-easy-events' ),
			'add_js_func' => (empty($add_js_func)) ? 0 : 1,
			'add_js_func_name' => $add_js_fname,
                        'add_js_func_p2p_type' => $add_js_p2p_type,
		) );

		self::$enqueued_scripts = true;

	}

	function render( $post ) {
		$extra_qv = array_merge( self::$admin_box_qv, array(
			'p2p:context' => 'admin_box',
			'p2p:per_page' => -1
		) );

		$this->connected_items = $this->ctype->get_connected( $post, $extra_qv, 'abstract' )->items;

		$data = array(
			'attributes' => $this->render_data_attributes(),
			'connections' => $this->render_connections_table( $post ),
			'create-connections' => $this->render_create_connections( $post ),
		);

		echo EMD_P2P_Mustache::render( 'box', $data );
	}

	protected function render_data_attributes() {
		$data_attr = array(
			'p2p_type' => $this->ctype->name,
			'duplicate_connections' => $this->ctype->duplicate_connections,
			'cardinality' => $this->ctype->get( 'opposite', 'cardinality' ),
			'direction' => $this->ctype->get_direction()
		);

		$data_attr_str = array();
		foreach ( $data_attr as $key => $value )
			$data_attr_str[] = "data-$key='" . $value . "'";

		return implode( ' ', $data_attr_str );
	}

	protected function render_connections_table( $post ) {
		$data = array();

		if ( empty( $this->connected_items ) )
			$data['hide'] = 'style="display:none"';

		$tbody = array();
		foreach ( $this->connected_items as $item ) {
			$tbody[] = $this->connection_row( $item->p2p_id, $item, false, $post->ID );
		}
		$data['tbody'] = $tbody;

		foreach ( $this->columns as $key => $field ) {
			$data['thead'][] = array(
				'column' => $key,
				'title' => $field->get_title()
			);
		}

		return $data;
	}

	protected function render_create_connections( $post ) {
		$data = array(
			'label' => $this->labels->create,
		);

		if ( 'one' == $this->ctype->get( 'opposite', 'cardinality' ) ) {
			if ( !empty( $this->connected_items ) )
				$data['hide'] = 'style="display:none"';
		}

		// Search tab
		$tab_content = EMD_P2P_Mustache::render( 'tab-search', array(
			'placeholder' => $this->labels->search_items,
			'candidates' => $this->post_rows( $post->ID )
		) );

		$data['tabs'][] = array(
			'tab-id' => 'search',
			'tab-title' => __( 'Search', 'wp-easy-events' ),
			'is-active' => array(true),
			'tab-content' => $tab_content
		);

		// Create post tab
		if ( $this->can_create_post() ) {
			$tab_content = EMD_P2P_Mustache::render( 'tab-create-post', array(
				'title' => $this->labels->add_new_item
			) );

			$data['tabs'][] = array(
				'tab-id' => 'create-post',
				'tab-title' => $this->labels->new_item,
				'tab-content' => $tab_content
			);
		}

		$data['show-tab-headers'] = count( $data['tabs'] ) > 1 ? array(true) : false;

		return $data;
	}

	protected function connection_row( $p2p_id, $item, $render = false, $current_post_id ) {
		return $this->table_row( $this->columns, $p2p_id, $item, $render, $current_post_id );
	}

	protected function table_row( $columns, $p2p_id, $item, $render = false, $current_post_id ) {
		$data = array();
		$item->post_title = apply_filters( 'emd_ext_p2p_connect_title', $item->get_title(), $item->get_object(), $current_post_id );

		foreach ( $columns as $key => $field ) {
			$data['columns'][] = array(
				'column' => $key,
				'content' => $field->render( $p2p_id, $item )
			);
		}

		if ( !$render )
			return $data;

		return EMD_P2P_Mustache::render( 'table-row', $data );
	}

	protected function post_rows( $current_post_id, $page = 1, $search = '' ) {
		$extra_qv = array_merge( self::$admin_box_qv, array(
			'p2p:search' => $search,
			'p2p:page' => $page,
			'p2p:per_page' => 5,
			'orderby' => 'title',
			'order' => 'ASC',
		) );
		$extra_qv = apply_filters('emd_ext_p2p_limit_rows',$extra_qv,$current_post_id,$this->ctype);

		$candidate = $this->ctype->get_connectable( $current_post_id, $extra_qv, 'abstract' );

		if ( empty( $candidate->items ) ) {
			return emd_p2p_html( 'div class="p2p-notice"', $this->labels->not_found );
		}

		$data = array();

		$columns = array(
			'create' => new P2P_Field_Create( $this->columns['title'] ),
		);

		foreach ( $candidate->items as $item ) {
			$data['rows'][] = $this->table_row( $columns, 0, $item, false, $current_post_id );
		}

		if ( $candidate->total_pages > 1 ) {
			$data['navigation'] = array(
				'current-page' => number_format_i18n( $candidate->current_page ),
				'total-pages' => number_format_i18n( $candidate->total_pages ),

				'current-page-raw' => $candidate->current_page,
				'total-pages-raw' => $candidate->total_pages,

				'prev-inactive' => ( 1 == $candidate->current_page ) ? 'inactive' : '',
				'next-inactive' => ( $candidate->total_pages == $candidate->current_page ) ? 'inactive' : '',

				'prev-label' =>  __( 'previous', 'wp-easy-events' ),
				'next-label' =>  __( 'next', 'wp-easy-events' ),
				'of-label' => __( 'of', 'wp-easy-events' ),
			);
		}

		return EMD_P2P_Mustache::render( 'tab-list', $data );
	}


	// Ajax handlers

	public function ajax_create_post() {
		if ( !$this->can_create_post() )
			die( -1 );

		$args = array(
			'post_title' => sanitize_text_field ($_POST['post_title']),
			'post_author' => get_current_user_id(),
			'post_type' => $this->ctype->get( 'opposite', 'side' )->first_post_type()
		);

		$from = absint( $_POST['from'] );

		$args = apply_filters( 'p2p_new_post_args', $args, $this->ctype, $from );

		$this->safe_connect( wp_insert_post( $args ) );
	}

	public function ajax_connect() {
		$this->safe_connect( absint($_POST['to']) );
	}

	private function safe_connect( $to ) {
		$from = absint( $_POST['from'] );
		$to = absint( $to );

		if ( !$from || !$to )
			die(-1);

		$p2p_id = $this->ctype->connect( $from, $to );

		self::maybe_send_error( $p2p_id );

		$item = $this->ctype->get( 'opposite','side')->item_recognize( $to );

		$out = array(
			'row' => $this->connection_row( $p2p_id, $item, true, $from )
		);

		die( json_encode( $out ) );
	}

	public function ajax_disconnect() {
		if(!empty($_POST['p2p_id'])){
			$p2p_ids = array_map( 'absint', (array) $_POST['p2p_id'] );
			emd_p2p_delete_connection( $p2p_ids );
		}
		$this->refresh_candidates();
	}

	public function ajax_clear_connections() {
		$r = $this->ctype->disconnect( absint($_POST['from']), 'any' );

		self::maybe_send_error( $r );

		$this->refresh_candidates();
	}

	protected static function maybe_send_error( $r ) {
		if ( !is_wp_error( $r ) )
			return;

		$out = array(
			'error' => $r->get_error_message()
		);

		die( json_encode( $out ) );
	}

	public function ajax_search() {
		$this->refresh_candidates();
	}

	private function refresh_candidates() {
		$rows = $this->post_rows( absint($_REQUEST['from']), absint($_REQUEST['paged']), sanitize_text_field($_REQUEST['s']) );

		$results = compact( 'rows' );

		die( json_encode( $results ) );
	}

	protected function can_create_post() {
		if ( !$this->args->can_create_post )
			return false;

		$side = $this->ctype->get( 'opposite', 'side' );

		return $side->can_create_item();
	}
}

