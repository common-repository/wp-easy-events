<?php
/**
 * Entity Class
 *
 * @package WP_EASY_EVENTS
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Emd_Event_Venues Class
 * @since WPAS 4.0
 */
class Emd_Event_Venues extends Emd_Entity {
	protected $post_type = 'emd_event_venues';
	protected $app = 'wp_easy_events';
	protected $sing_label;
	protected $plural_label;
	protected $menu_entity;
	protected $id;
	/**
	 * Initialize entity class
	 *
	 * @since WPAS 4.0
	 *
	 */
	public function __construct() {
		add_action('init', array(
			$this,
			'set_filters'
		) , 1);
		add_action('admin_init', array(
			$this,
			'set_metabox'
		));
		add_filter('post_updated_messages', array(
			$this,
			'updated_messages'
		));
		add_action('admin_menu', array(
			$this,
			'add_menu_link'
		));
		add_action('admin_head-edit.php', array(
			$this,
			'add_opt_button'
		));
		add_action('admin_menu', array(
			$this,
			'add_top_menu_link'
		) , 1);
		$is_adv_filt_ext = apply_filters('emd_adv_filter_on', 0);
		if ($is_adv_filt_ext === 0) {
			add_action('manage_emd_event_venues_posts_custom_column', array(
				$this,
				'custom_columns'
			) , 10, 2);
			add_filter('manage_emd_event_venues_posts_columns', array(
				$this,
				'column_headers'
			));
		}
		add_filter('post_row_actions', array(
			$this,
			'duplicate_link'
		) , 10, 2);
		add_action('admin_action_emd_duplicate_entity', array(
			$this,
			'duplicate_entity'
		));
	}
	public function change_title_disable_emd_temp($title, $id) {
		$post = get_post($id);
		if ($this->post_type == $post->post_type && (!empty($this->id) && $this->id == $id)) {
			return '';
		}
		return $title;
	}
	/**
	 * Get column header list in admin list pages
	 * @since WPAS 4.0
	 *
	 * @param array $columns
	 *
	 * @return array $columns
	 */
	public function column_headers($columns) {
		$ent_list = get_option($this->app . '_ent_list');
		if (!empty($ent_list[$this->post_type]['featured_img'])) {
			$columns['featured_img'] = __('Featured Image', 'wp-easy-events');
		}
		foreach ($this->boxes as $mybox) {
			foreach ($mybox['fields'] as $fkey => $mybox_field) {
				if (!in_array($fkey, Array(
					'wpas_form_name',
					'wpas_form_submitted_by',
					'wpas_form_submitted_ip'
				)) && !in_array($mybox_field['type'], Array(
					'textarea',
					'wysiwyg'
				)) && $mybox_field['list_visible'] == 1) {
					$columns[$fkey] = $mybox_field['name'];
				}
			}
		}
		$taxonomies = get_object_taxonomies($this->post_type, 'objects');
		if (!empty($taxonomies)) {
			$tax_list = get_option($this->app . '_tax_list');
			foreach ($taxonomies as $taxonomy) {
				if (!empty($tax_list[$this->post_type][$taxonomy->name]) && $tax_list[$this->post_type][$taxonomy->name]['list_visible'] == 1) {
					$columns[$taxonomy->name] = $taxonomy->label;
				}
			}
		}
		$rel_list = get_option($this->app . '_rel_list');
		if (!empty($rel_list)) {
			foreach ($rel_list as $krel => $rel) {
				if ($rel['from'] == $this->post_type && in_array($rel['show'], Array(
					'any',
					'from'
				))) {
					$columns[$krel] = $rel['from_title'];
				} elseif ($rel['to'] == $this->post_type && in_array($rel['show'], Array(
					'any',
					'to'
				))) {
					$columns[$krel] = $rel['to_title'];
				}
			}
		}
		return $columns;
	}
	/**
	 * Get custom column values in admin list pages
	 * @since WPAS 4.0
	 *
	 * @param int $column_id
	 * @param int $post_id
	 *
	 * @return string $value
	 */
	public function custom_columns($column_id, $post_id) {
		if (taxonomy_exists($column_id) == true) {
			$terms = get_the_terms($post_id, $column_id);
			$ret = array();
			if (!empty($terms)) {
				foreach ($terms as $term) {
					$url = add_query_arg(array(
						'post_type' => $this->post_type,
						'term' => $term->slug,
						'taxonomy' => $column_id
					) , admin_url('edit.php'));
					$a_class = preg_replace('/^emd_/', '', $this->post_type);
					$ret[] = sprintf('<a href="%s"  class="' . esc_attr($a_class) . '-tax ' . esc_attr($term->slug) . '">%s</a>', esc_url($url) , esc_html($term->name));
				}
			}
			echo wp_kses_post(implode(', ', $ret));
			return;
		}
		$rel_list = get_option($this->app . '_rel_list');
		if (!empty($rel_list) && !empty($rel_list[$column_id])) {
			$rel_arr = $rel_list[$column_id];
			if ($rel_arr['from'] == $this->post_type) {
				$other_ptype = $rel_arr['to'];
			} elseif ($rel_arr['to'] == $this->post_type) {
				$other_ptype = $rel_arr['from'];
			}
			$column_id = str_replace('rel_', '', $column_id);
			if (function_exists('p2p_type') && emd_p2p_type($column_id)) {
				$rel_args = apply_filters('emd_ext_p2p_add_query_vars', array(
					'posts_per_page' => - 1
				) , Array(
					$other_ptype
				));
				$connected = emd_p2p_type($column_id)->get_connected($post_id, $rel_args);
				$ptype_obj = get_post_type_object($this->post_type);
				$edit_cap = $ptype_obj->cap->edit_posts;
				$ret = array();
				if (empty($connected->posts)) return '&ndash;';
				foreach ($connected->posts as $myrelpost) {
					$rel_title = get_the_title($myrelpost->ID);
					$rel_title = apply_filters('emd_ext_p2p_connect_title', $rel_title, $myrelpost, '');
					$url = get_permalink($myrelpost->ID);
					$url = apply_filters('emd_ext_connected_ptype_url', $url, $myrelpost, $edit_cap);
					$ret[] = sprintf('<a href="%s" title="%s" target="_blank">%s</a>', esc_url($url) , esc_attr($rel_title) , esc_html($rel_title));
				}
				echo wp_kses_post(implode(', ', $ret));
				return;
			}
		}
		$value = get_post_meta($post_id, $column_id, true);
		$type = "";
		foreach ($this->boxes as $mybox) {
			foreach ($mybox['fields'] as $fkey => $mybox_field) {
				if ($fkey == $column_id) {
					$type = $mybox_field['type'];
					break;
				}
			}
		}
		if ($column_id == 'featured_img') {
			$type = 'featured_img';
		}
		switch ($type) {
			case 'featured_img':
				$thumb_url = wp_get_attachment_image_src(get_post_thumbnail_id($post_id) , 'thumbnail');
				if (!empty($thumb_url)) {
					$value = "<img style='max-width:100%;height:auto;' src='" . esc_url($thumb_url[0]) . "' >";
				}
			break;
			case 'plupload_image':
			case 'image':
			case 'thickbox_image':
				$image_list = emd_mb_meta($column_id, 'type=image');
				$value = "";
				if (!empty($image_list)) {
					$myimage = current($image_list);
					$value = "<img style='max-width:100%;height:auto;' src='" . esc_url($myimage['url']) . "' >";
				}
			break;
			case 'user':
			case 'user-adv':
				$user_id = emd_mb_meta($column_id);
				if (!empty($user_id)) {
					$user_info = get_userdata($user_id);
					$value = $user_info->display_name;
				}
			break;
			case 'file':
				$file_list = emd_mb_meta($column_id, 'type=file');
				if (!empty($file_list)) {
					$value = "";
					foreach ($file_list as $myfile) {
						$fsrc = wp_mime_type_icon($myfile['ID']);
						$value.= "<a style='margin:5px;' href='" . esc_url($myfile['url']) . "' target='_blank'><img src='" . esc_url($fsrc) . "' title='" . esc_attr($myfile['name']) . "' width='20' /></a>";
					}
				}
			break;
			case 'radio':
			case 'checkbox_list':
			case 'select':
			case 'select_advanced':
				$value = emd_get_attr_val($this->app, $post_id, $this->post_type, $column_id);
			break;
			case 'checkbox':
				if ($value == 1) {
					$value = '<span class="dashicons dashicons-yes"></span>';
				} elseif ($value == 0) {
					$value = '<span class="dashicons dashicons-no-alt"></span>';
				}
			break;
			case 'rating':
				$value = apply_filters('emd_get_rating_value', $value, Array(
					'meta' => $column_id
				) , $post_id);
			break;
		}
		if (is_array($value)) {
			$value = "<div class='clonelink'>" . implode("</div><div class='clonelink'>", $value) . "</div>";
		}
		echo wp_kses_post($value);
	}
	/**
	 * Register post type and taxonomies and set initial values for taxs
	 *
	 * @since WPAS 4.0
	 *
	 */
	public static function register() {
		$labels = array(
			'name' => __('Venues', 'wp-easy-events') ,
			'singular_name' => __('Venue', 'wp-easy-events') ,
			'add_new' => __('Add New', 'wp-easy-events') ,
			'add_new_item' => __('Add New Venue', 'wp-easy-events') ,
			'edit_item' => __('Edit Venue', 'wp-easy-events') ,
			'new_item' => __('New Venue', 'wp-easy-events') ,
			'all_items' => __('All Venues', 'wp-easy-events') ,
			'view_item' => __('View Venue', 'wp-easy-events') ,
			'search_items' => __('Search Venues', 'wp-easy-events') ,
			'not_found' => __('No Venues Found', 'wp-easy-events') ,
			'not_found_in_trash' => __('No Venues Found In Trash', 'wp-easy-events') ,
			'menu_name' => __('Venues', 'wp-easy-events') ,
		);
		$ent_map_list = get_option('wp_easy_events_ent_map_list', Array());
		$myrole = emd_get_curr_usr_role('wp_easy_events');
		if (!empty($ent_map_list['emd_event_venues']['rewrite'])) {
			$rewrite = $ent_map_list['emd_event_venues']['rewrite'];
		} else {
			$rewrite = 'venues';
		}
		$supports = Array();
		if (empty($ent_map_list['emd_event_venues']['attrs']['blt_title']) || $ent_map_list['emd_event_venues']['attrs']['blt_title'] != 'hide') {
			if (empty($ent_map_list['emd_event_venues']['edit_attrs'])) {
				$supports[] = 'title';
			} elseif ($myrole == 'administrator') {
				$supports[] = 'title';
			} elseif ($myrole != 'administrator' && !empty($ent_map_list['emd_event_venues']['edit_attrs'][$myrole]['blt_title']) && $ent_map_list['emd_event_venues']['edit_attrs'][$myrole]['blt_title'] == 'edit') {
				$supports[] = 'title';
			}
		}
		if (empty($ent_map_list['emd_event_venues']['attrs']['blt_content']) || $ent_map_list['emd_event_venues']['attrs']['blt_content'] != 'hide') {
			if (empty($ent_map_list['emd_event_venues']['edit_attrs'])) {
				$supports[] = 'editor';
			} elseif ($myrole == 'administrator') {
				$supports[] = 'editor';
			} elseif ($myrole != 'administrator' && !empty($ent_map_list['emd_event_venues']['edit_attrs'][$myrole]['blt_content']) && $ent_map_list['emd_event_venues']['edit_attrs'][$myrole]['blt_content'] == 'edit') {
				$supports[] = 'editor';
			}
		}
		if (empty($ent_map_list['emd_event_venues']['attrs']['featured_img']) || $ent_map_list['emd_event_venues']['attrs']['featured_img'] != 'hide') {
			if (empty($ent_map_list['emd_event_venues']['edit_attrs'])) {
				$supports[] = 'thumbnail';
			} elseif ($myrole == 'administrator') {
				$supports[] = 'thumbnail';
			} elseif ($myrole != 'administrator' && !empty($ent_map_list['emd_event_venues']['edit_attrs'][$myrole]['featured_img']) && $ent_map_list['emd_event_venues']['edit_attrs'][$myrole]['featured_img'] == 'edit') {
				$supports[] = 'thumbnail';
			}
		}
		if (empty($ent_map_list['emd_event_venues']['attrs']['blt_excerpt']) || $ent_map_list['emd_event_venues']['attrs']['blt_excerpt'] != 'hide') {
			if (empty($ent_map_list['emd_event_venues']['edit_attrs'])) {
				$supports[] = 'excerpt';
			} elseif ($myrole == 'administrator') {
				$supports[] = 'excerpt';
			} elseif ($myrole != 'administrator' && !empty($ent_map_list['emd_event_venues']['edit_attrs'][$myrole]['blt_excerpt']) && $ent_map_list['emd_event_venues']['edit_attrs'][$myrole]['blt_excerpt'] == 'edit') {
				$supports[] = 'excerpt';
			}
		}
		register_post_type('emd_event_venues', array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'description' => __('', 'wp-easy-events') ,
			'show_in_menu' => '',
			'menu_position' => null,
			'has_archive' => false,
			'exclude_from_search' => false,
			'rewrite' => array(
				'slug' => $rewrite
			) ,
			'can_export' => true,
			'show_in_rest' => false,
			'hierarchical' => false,
			'map_meta_cap' => 'false',
			'taxonomies' => array() ,
			'capability_type' => 'post',
			'supports' => $supports,
		));
	}
	/**
	 * Set metabox fields,labels,filters, comments, relationships if exists
	 *
	 * @since WPAS 4.0
	 *
	 */
	public function set_filters() {
		do_action('emd_ext_class_init', $this);
		$search_args = Array();
		$filter_args = Array();
		$this->sing_label = __('Venue', 'wp-easy-events');
		$this->plural_label = __('Venues', 'wp-easy-events');
		$this->menu_entity = 'emd_wpe_event';
		$this->boxes['emd_event_venues_info_emd_event_venues_0'] = array(
			'id' => 'emd_event_venues_info_emd_event_venues_0',
			'title' => __('Venue Info', 'wp-easy-events') ,
			'app_name' => 'wp_easy_events',
			'pages' => array(
				'emd_event_venues'
			) ,
			'context' => 'normal',
		);
		list($search_args, $filter_args) = $this->set_args_boxes();
		if (!post_type_exists($this->post_type) || in_array($this->post_type, Array(
			'post',
			'page'
		))) {
			self::register();
		}
		do_action('emd_set_adv_filtering', $this->post_type, $search_args, $this->boxes, $filter_args, $this->app, $this->plural_label);
		add_action('admin_notices', array(
			$this,
			'show_lite_filters'
		));
		$ent_map_list = get_option($this->app . '_ent_map_list');
		if (!function_exists('p2p_register_connection_type')) {
			return;
		}
		$rel_list = get_option($this->app . '_rel_list');
		$myrole = emd_get_curr_usr_role('wp_easy_events');
		if (empty($ent_map_list['emd_event_venues']['hide_rels']['rel_event_venue']) || $ent_map_list['emd_event_venues']['hide_rels']['rel_event_venue'] != 'hide') {
			if ($myrole != 'administrator' && !empty($ent_map_list['emd_event_venues']['edit_rels'][$myrole]['rel_event_venue']) && $ent_map_list['emd_event_venues']['edit_rels'][$myrole]['rel_event_venue'] != 'edit') {
				$admin_box = 'none';
			} else {
				$admin_box = array(
					'show' => 'any',
					'context' => 'advanced'
				);
			}
			$rel_fields = Array();
			p2p_register_connection_type(array(
				'name' => 'event_venue',
				'from' => 'emd_event_venues',
				'to' => 'emd_wpe_event',
				'sortable' => 'any',
				'reciprocal' => false,
				'cardinality' => 'one-to-many',
				'title' => array(
					'from' => __('Events', 'wp-easy-events') ,
					'to' => __('Venues', 'wp-easy-events')
				) ,
				'from_labels' => array(
					'singular_name' => __('Venue', 'wp-easy-events') ,
					'search_items' => __('Search Venues', 'wp-easy-events') ,
					'not_found' => __('No Venues found.', 'wp-easy-events') ,
				) ,
				'to_labels' => array(
					'singular_name' => __('Event', 'wp-easy-events') ,
					'search_items' => __('Search Events', 'wp-easy-events') ,
					'not_found' => __('No Events found.', 'wp-easy-events') ,
				) ,
				'fields' => $rel_fields,
				'admin_box' => $admin_box,
			));
		}
	}
	/**
	 * Initialize metaboxes
	 * @since WPAS 4.5
	 *
	 */
	public function set_metabox() {
		if (class_exists('EMD_Meta_Box') && is_array($this->boxes)) {
			foreach ($this->boxes as $meta_box) {
				new EMD_Meta_Box($meta_box);
			}
		}
	}
	/**
	 * Change content for created frontend views
	 * @since WPAS 4.0
	 * @param string $content
	 *
	 * @return string $content
	 */
	public function change_content($content) {
		global $post;
		$layout = "";
		$this->id = $post->ID;
		$tools = get_option('wp_easy_events_tools');
		if (!empty($tools['disable_emd_templates'])) {
			add_filter('the_title', array(
				$this,
				'change_title_disable_emd_temp'
			) , 10, 2);
		}
		if (get_post_type() == $this->post_type && is_single()) {
			ob_start();
			do_action('emd_single_before_content', $this->app, $this->post_type);
			emd_get_template_part($this->app, 'single', 'emd-event-venues');
			do_action('emd_single_after_content', $this->app, $this->post_type);
			$layout = ob_get_clean();
		}
		if ($layout != "") {
			$content = $layout;
		}
		if (!empty($tools['disable_emd_templates'])) {
			remove_filter('the_title', array(
				$this,
				'change_title_disable_emd_temp'
			) , 10, 2);
		}
		return $content;
	}
	/**
	 * Add operations and add new submenu hook
	 * @since WPAS 4.4
	 */
	public function add_menu_link() {
		add_submenu_page(null, __('CSV Import/Export', 'wp-easy-events') , __('CSV Import/Export', 'wp-easy-events') , 'manage_operations_emd_event_venuess', 'operations_emd_event_venues', array(
			$this,
			'get_operations'
		));
	}
	/**
	 * Display operations page
	 * @since WPAS 4.0
	 */
	public function get_operations() {
		if (current_user_can('manage_operations_emd_event_venuess')) {
			if (!function_exists('emd_operations_entity')) {
				emd_lite_get_operations('opr', $this->plural_label, $this->app);
			} else {
				do_action('emd_operations_entity', $this->post_type, $this->plural_label, $this->sing_label, $this->app, $this->menu_entity);
			}
		}
	}
	/**
	 * Add new submenu hook
	 * @since WPAS 4.4
	 */
	public function add_top_menu_link() {
		add_submenu_page('edit.php?post_type=emd_wpe_event', __('Venues', 'wp-easy-events') , __('All Venues', 'wp-easy-events') , 'edit_emd_event_venuess', 'edit.php?post_type=emd_event_venues', false);
		add_submenu_page('edit.php?post_type=emd_wpe_event', __('Venues', 'wp-easy-events') , __('Add New Venue', 'wp-easy-events') , 'edit_emd_event_venuess', 'post-new.php?post_type=emd_event_venues', false);
		do_action('emd_add_submenu_pages', $this->post_type, $this->app, $this->menu_entity);
	}
	public function show_lite_filters() {
		if (class_exists('EMD_AFC')) {
			return;
		}
		global $pagenow;
		if (get_post_type() == $this->post_type && $pagenow == 'edit.php') {
			emd_lite_get_filters();
		}
	}
}
new Emd_Event_Venues;
