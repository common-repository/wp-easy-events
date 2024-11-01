<?php
/**
 * Emd Notifications
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       1.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Emd_Notifications Class
 * Display and register notifications to settings
 *
 * @since WPAS 4.0
 */
class Emd_Notifications {
	var $app = "";
	var $notify_list;
	var $notify_init_list;
	var $ent_list;
	var $attr_list;
	var $glob_list;
	var $com_list;
	var $tax_list;
	var $rel_list;
	var $event_group;
	var $notify_field_list;
	/**
	 * Instantiate notification class
	 * Set notify,ent,tax,rel and attr list,set notify field list
	 * @since WPAS 4.0
	 *
	 * @param string $entity
	 * @param string $myapp
	 *
	 */
	public function __construct($app) {
		add_action('emd_display_settings_notify', array(
			'Emd_Notifications',
			'display_settings_notify'
		) , 10, 2);
		$this->app = $app;
		$this->notify_list = get_option($app . "_notify_list");
		$this->notify_init_list = get_option($app . "_notify_init_list");
		$this->glob_list = get_option($app . '_glob_list');
		$this->com_list = get_option($app . '_comment_list');
		$this->attr_list = get_option($app . '_attr_list');
		$this->tax_list = get_option($app . '_tax_list');
		$this->rel_list = get_option($app . '_rel_list');
		$this->ent_list = get_option($app . '_ent_list');
		$this->notify_field_list = Array(
			'active' => array(
				'id' => 'active',
				'name' => __('Active', 'wp-easy-events') ,
				'info' => '',
				'type' => 'checkbox',
				'isset' => 1
			) ,
			'ev_front_add' => array(
				'id' => 'ev_front_add',
				'name' => __('Frontend Add', 'wp-easy-events') ,
				'info' => '',
				'type' => 'checkbox',
				'group' => 'event',
				'isset' => 1
			) ,
			'ev_back_add' => array(
				'id' => 'ev_back_add',
				'name' => __('Backend Add', 'wp-easy-events') ,
				'info' => '',
				'type' => 'checkbox',
				'group' => 'event',
				'isset' => 1
			) ,
			'ev_change' => array(
				'id' => 'ev_change',
				'name' => __('Change', 'wp-easy-events') ,
				'info' => '',
				'type' => 'checkbox',
				'group' => 'event',
				'isset' => 1
			) ,
			'ev_delete' => array(
				'id' => 'ev_delete',
				'name' => __('Delete', 'wp-easy-events') ,
				'info' => '',
				'type' => 'checkbox',
				'group' => 'event',
				'isset' => 1
			) ,
			'ev_add' => array(
				'id' => 'ev_add',
				'name' => __('Add', 'wp-easy-events') ,
				'info' => '',
				'type' => 'checkbox',
				'group' => 'event',
				'isset' => 1
			) ,
			'ev_trash' => array(
				'id' => 'ev_trash',
				'name' => __('Trash', 'wp-easy-events') ,
				'info' => '',
				'type' => 'checkbox',
				'group' => 'event',
				'isset' => 1
			) ,
			'ev_change_val' => array(
				'id' => 'ev_change_val',
				'name' => __('Change Value', 'wp-easy-events') ,
				'info' => __('If change value is empty, this notification is triggered in every value change.', 'wp-easy-events') ,
				'type' => 'text',
				'isset' => 1
			) ,
			'user_msg' => array(
				'id' => 'user_msg',
				'name' => __('User Message', 'wp-easy-events') ,
				'info' => '',
				'type' => 'header',
				'isset' => 0
			) ,
			'user_send_to' => array(
				'id' => 'user_send_to',
				'name' => __('Send To', 'wp-easy-events') ,
				'info' => '',
				'type' => 'multicheck',
				'mtype' => 'user',
				'isset' => 0
			) ,
			'user_from_name' => array(
				'id' => 'user_from_name',
				'name' => __('From Name', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'user',
				'isset' => 0
			) ,
			'user_from_email' => array(
				'id' => 'user_from_email',
				'name' => __('From Email', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'user',
				'isset' => 0
			) ,
			'user_reply_to' => array(
				'id' => 'user_reply_to',
				'name' => __('Reply To', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'user',
				'isset' => 0
			) ,
			'user_cc' => array(
				'id' => 'user_cc',
				'name' => __('Cc', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'user',
				'isset' => 0
			) ,
			'user_bcc' => array(
				'id' => 'user_bcc',
				'name' => __('BCc', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'user',
				'isset' => 0
			) ,
			'user_subject' => array(
				'id' => 'user_subject',
				'name' => __('Subject', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'user',
				'isset' => 0
			) ,
			'user_message' => array(
				'id' => 'user_message',
				'name' => __('Message', 'wp-easy-events') ,
				'info' => '',
				'type' => 'editor',
				'mtype' => 'user',
				'isset' => 0
			) ,
			'user_fields' => array(
				'id' => 'user_fields',
				'name' => '',
				'info' => '',
				'type' => 'static',
				'mtype' => 'user',
				'isset' => 0
			) ,
			'admin_msg' => array(
				'id' => 'admin_msg',
				'name' => __('Admin Message', 'wp-easy-events') ,
				'info' => '',
				'type' => 'header',
				'isset' => 0
			) ,
			'admin_send_to' => array(
				'id' => 'admin_send_to',
				'name' => __('Send To', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'admin',
				'isset' => 0
			) ,
			'admin_from_name' => array(
				'id' => 'admin_from_name',
				'name' => __('From Name', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'admin',
				'isset' => 0
			) ,
			'admin_from_email' => array(
				'id' => 'admin_from_email',
				'name' => __('From Email', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'admin',
				'isset' => 0
			) ,
			'admin_reply_to' => array(
				'id' => 'admin_reply_to',
				'name' => __('Reply To', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'admin',
				'isset' => 0
			) ,
			'admin_cc' => array(
				'id' => 'admin_cc',
				'name' => __('Cc', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'admin',
				'isset' => 0
			) ,
			'admin_bcc' => array(
				'id' => 'admin_bcc',
				'name' => __('BCc', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'admin',
				'isset' => 0
			) ,
			'admin_subject' => array(
				'id' => 'admin_subject',
				'name' => __('Subject', 'wp-easy-events') ,
				'info' => '',
				'type' => 'text',
				'mtype' => 'admin',
				'isset' => 0
			) ,
			'admin_message' => array(
				'id' => 'admin_message',
				'name' => __('Message', 'wp-easy-events') ,
				'info' => '',
				'type' => 'editor',
				'mtype' => 'admin',
				'isset' => 0
			) ,
			'admin_fields' => array(
				'id' => 'admin_fields',
				'name' => '',
				'info' => '',
				'type' => 'static',
				'mtype' => 'admin',
				'isset' => 0
			) ,
		);
		$this->event_group = Array(
			'ev_front_add',
			'ev_back_add',
			'ev_change',
			'ev_add',
			'ev_trash',
			'ev_delete'
		);
	}
	/**
	 * Register notifications to setting
	 * @since WPAS 4.0
	 *
	 *
	 */
	public function register_settings() {
		if (!empty($this->notify_init_list)) {
			register_setting($this->app . '_notify_list', $this->app . '_notify_list', array(
				$this,
				'settings_sanitize'
			));
			foreach ($this->notify_init_list as $knotify => $vnotify) {
				if(!empty($this->notify_list) && !empty($this->notify_list[$knotify])) {
					$vnotify = $this->notify_list[$knotify];
				}
				add_settings_section('emd_setting_notify_' . $knotify, __return_null() , 'emd_setting_notifycallback', 'emd_setting_notify_' . $knotify);
				$count_event = 0;
				foreach ($this->event_group as $event) {
					if (isset($vnotify[$event])) {
						$count_event++;
					}
				}
				foreach ($this->notify_field_list as $nfield) {
					$no_show = 0;
					if ((!isset($nfield['group']) || ($nfield['group'] == 'event' && $count_event > 1)) && ($nfield['isset'] == 0 || ($nfield['isset'] == 1 && isset($vnotify[$nfield['id']])))) {
						$fid = $this->app . '_notify_list[' . $knotify . '][' . $nfield['id'] . ']';
						$field_val = '';
						$nfield['name'] = $this->set_padding($nfield['name'], $nfield['info'], 0);
						if (isset($nfield['mtype'])) {
							$id = str_replace($nfield['mtype'] . "_", "", $nfield['id']);
							$fid = $this->app . '_notify_list[' . $knotify . '][' . $nfield['mtype'] . '_msg][' . $id . ']';
							if (isset($vnotify[$nfield['mtype'] . '_msg'][$id])) {
								$field_val = $vnotify[$nfield['mtype'] . '_msg'][$id];
							}
							$nfield['name'] = $this->set_padding($nfield['name'], $nfield['info'], 1);
						}
						if (isset($vnotify[$nfield['id']])) {
							$field_val = $vnotify[$nfield['id']];
						}
						$args = Array(
							'key' => $fid,
							'val' => $field_val
						);
						if (isset($vnotify['user_msg']['send_to']) && $nfield['id'] == 'user_send_to') {
							$args['options'] = $vnotify['user_msg']['send_to'];
						}
						if ($nfield['type'] == 'editor') {
							$args['editor_id'] = $knotify . "_" . $nfield['mtype'];
						}
						if ($nfield['type'] == 'static') {
							$args['notify_id'] = $knotify;
							$args['mtype'] = $nfield['mtype'];
						}
						if (isset($nfield['mtype'])) {
							if (empty($vnotify['user_msg']) && $nfield['mtype'] == 'user') {
								$no_show = 1;
							} elseif (empty($vnotify['admin_msg']) && $nfield['mtype'] == 'admin') {
								$no_show = 1;
							}
						} elseif ($nfield['id'] == 'admin_msg' && empty($vnotify['admin_msg'])) {
							$no_show = 1;
						} elseif ($nfield['id'] == 'user_msg' && empty($vnotify['user_msg'])) {
							$no_show = 1;
						}
						if ($no_show == 0) {
							add_settings_field($fid, $nfield['name'], array(
								$this,
								'notify_' . $nfield['type'] . '_callback'
							) , 'emd_setting_notify_' . $knotify, 'emd_setting_notify_' . $knotify, $args);
						}
					}
				}
			}
		}
	}
	/**
	 * Sanitize and process input entered from settings page
	 * @since WPAS 4.0
	 *
	 * @param array $input
	 *
	 * @return array $notify_list
	 *
	 */
	public function settings_sanitize($input) {
		if(!empty($this->notify_init_list)){
			foreach ($this->notify_init_list as $knot => $vnot) {
				$count_event = 0;
				foreach ($this->event_group as $event) {
					if (isset($vnot[$event])) {
						$count_event++;
					}
				}
				foreach ($this->notify_field_list as $nfield) {
					if (isset($nfield['mtype'])) {
						$mid = str_replace($nfield['mtype'] . "_", "", $nfield['id']);
					}
					if (($nfield['type'] == 'checkbox' && isset($vnot[$nfield['id']]) && !isset($input[$knot][$nfield['id']])) && (!isset($nfield['group']) || ($nfield['group'] == 'event' && $count_event > 1))) {
						$vnot[$nfield['id']] = 0;
					} elseif ($nfield['type'] == 'multicheck' && !empty($vnot[$nfield['mtype'] . "_msg"])) {
						foreach ($vnot[$nfield['mtype'] . "_msg"][$mid] as $mkey => $mval) {
							if (isset($input[$knot][$nfield['mtype'] . "_msg"][$mid][$mkey])) {
								$mval['active'] = 1;
							} else {
								$mval['active'] = 0;
							}
							$vnot[$nfield['mtype'] . "_msg"][$mid][$mkey] = $mval;
						}
					} elseif (isset($nfield['mtype']) && isset($input[$knot][$nfield['mtype'] . "_msg"][$mid])) {
						$vnot[$nfield['mtype'] . "_msg"][$mid] = $input[$knot][$nfield['mtype'] . "_msg"][$mid];
					} elseif (isset($input[$knot][$nfield['id']]) && $nfield['type'] != 'header') {
						$vnot[$nfield['id']] = $input[$knot][$nfield['id']];
					}
				}
				$this->notify_init_list[$knot] = $vnot;
			}
			return $this->notify_init_list;
		}
		return $input;
	}
	/**
	 * Display checkbox type on settings page
	 * @since WPAS 4.0
	 *
	 * @param array $args
	 *
	 * @return string $html
	 *
	 */
	public function notify_checkbox_callback($args) {
		$checked = isset($args['val']) && $args['val'] == 1 ? 'checked' : '';
		echo '<input type="checkbox" id="' . esc_attr($args['key']) . '" name="' . esc_attr($args['key']) . '" value="1" ' . esc_attr($checked) . '/>';
	}
	/**
	 * Display text type on settings page
	 * @since WPAS 4.0
	 *
	 * @param array $args
	 *
	 * @return string $html
	 *
	 */
	public function notify_text_callback($args) {
		$size = 'regular';
		echo '<input type="text" class="' . esc_attr($size) . '-text" id="' . esc_attr($args['key']) . '" name="' . esc_attr($args['key']) . '" value="' . esc_attr(stripslashes($args['val'])) . '"/>';
	}
	/**
	 * Display header type on settings page
	 * @since WPAS 4.0
	 *
	 * @return string $html
	 *
	 */
	public function notify_header_callback() {
		echo '';
	}
	/**
	 * Display multicheck type on settings page
	 * @since WPAS 4.0
	 *
	 * @param array $args
	 *
	 * @return string $html
	 *
	 */
	public function notify_multicheck_callback($args) {
		if (!empty($args['options'])) {
			foreach ($args['options'] as $key => $option) {
				if ($option['active'] == 1) {
					$checked = 'checked';
				} else {
					$checked = '';
				}
				echo '<input name="' . esc_attr($args['key']) . '[' . esc_attr($key) . ']' . '" id="' . esc_attr($args['key']) . '[' . esc_attr($key) . ']' . '" type="checkbox" value="1" ' . esc_attr($checked) . '/>&nbsp;';
				echo '<label for="' . esc_attr($args['key']) . '[' . esc_attr($key) . ']' . '">' . esc_html($option['label']) . '</label><br/>';
			}
		}
	}
	/**
	 * Display static type on settings page
	 * @since WPAS 4.0
	 *
	 * @param array $args
	 *
	 * @return string $html
	 *
	 */
	public function notify_static_callback($args) {
		$attrs = $taxs = $rels = $globs = $coms = "";
		$badd = 0;
		if(!empty($this->glob_list)){
			foreach($this->glob_list as $kglob => $vglob){
				$globs .= esc_html($vglob['label']) . ": <b>{" . esc_html($kglob) . "}</b> ";
			}
		}

		$lists = Array(
			'attrs' => $this->attr_list,
			'taxs' => $this->tax_list,
			'rels' => $this->rel_list
		);
		foreach ($lists as $klist => $vlist) {
			if ($this->notify_init_list[$args['notify_id']]['level'] == 'rel') {
				$rlist = $this->rel_list['rel_' . $this->notify_init_list[$args['notify_id']]['object']];
				$ents = Array(
					$rlist['from'],
					$rlist['to']
				);
			} else {
				$ents = Array(
					$this->notify_init_list[$args['notify_id']]['entity']
				);
			}
			foreach ($ents as $ment) {
				if ($klist == 'rels') {
					$ent_list = $vlist;
				} else {
					if(isset($vlist[$ment])){
						$ent_list = $vlist[$ment];
					}
				}
				if(!empty($ent_list)){
					foreach ($ent_list as $kent => $vent) {
						if ($klist == 'attrs' && preg_match('/^wpas_/', $kent) && $badd != 1) {
							//$builtin.= $vent['label'] . ": <b>{" . $kent . "}</b> ,";
							continue;
						} elseif ($klist == 'rels' && $ment != $vent['from'] && $ment != $vent['to']) {
							continue;
						} elseif ($klist == 'rels' && ($ment == $vent['from'] || $ment == $vent['to'])) {
							$vent['label'] = $vent['to_title'];
							if ($ment == $vent['from']) {
								$vent['label'] = $vent['from_title'];
							}
						}
						$$klist.= esc_html($vent['label']) . ": <b>{" . esc_html($kent) . "}</b> ";
						if($klist == 'attrs' && $vent['display_type'] == 'user'){
							$$klist.= sprintf(__('%s Login Username','wp-easy-events'),esc_html($this->ent_list[$ment]['label'])) . ": <b>{" . esc_html($ment) . "_login_username}</b> ";
							$$klist.= sprintf(__('%s Login Password Reset Link','wp-easy-events'),esc_html($this->ent_list[$ment]['label'])) . ": <b>{" . esc_html($ment) . "_login_password_reset_link}</b> ";
							$$klist.= sprintf(__('%s Verification Link','wp-easy-events'),esc_html($this->ent_list[$ment]['label'])) . ": <b>{" . esc_html($ment) . "_verify_link}</b> ";
						}
					}
				}
				$badd = 1;
			}
		}
		if(!empty($this->com_list)){
			foreach($ents as $vent){
				if(isset($this->com_list[$vent])){
					$coms = esc_html($this->com_list[$vent]['label']) . ": <b>{com_" . esc_html($this->com_list[$vent]['key']) . "}</b> ";
				}
			}
		}
		$globs = (empty($globs) ? __("Not available", 'wp-easy-events') : rtrim($globs, ","));
		$attrs = (empty($attrs) ? __("Not available", 'wp-easy-events') : rtrim($attrs, ","));
		$taxs = (empty($taxs) ? __("Not available", 'wp-easy-events') : rtrim($taxs, ","));
		$rels = (empty($rels) ? __("Not available", 'wp-easy-events') : rtrim($rels, ","));
		$coms = (empty($coms) ? __("Not available", 'wp-easy-events') : rtrim($coms, ","));
		$fields = "<table><tr><th colspan=2>";
		$fields.= sprintf(__('Use template tags below to customize your email. Taxonomy and relationship tags produce link(s) to the related record(s). For no link tag, add %s to relationship or taxonomy tag. For example, for {mytag%s} tag produces a no link version of the tag. Check glossary tab for definitions.', 'wp-easy-events') , '_nl', '_nl');
		$fields.= "</th></tr>";
		if ($this->notify_init_list[$args['notify_id']]['level'] == 'rel') {
			foreach ($ents as $ment) {
				$fields .= "<tr><th>" . esc_html($this->ent_list[$ment]['label']) . ' ' . __('Builtin', 'wp-easy-events') . "</th>";
				$builtin = __('Title', 'wp-easy-events') . ": <b>{" . esc_html($ment) . "_title}</b> " . __('Permalink', 'wp-easy-events') . ": <b>{" . esc_html($ment) . "_permalink}</b> " . __('Edit Link', 'wp-easy-events') . ": <b>{" . esc_html($ment) . "_edit_link}</b> " . __('Delete Link', 'wp-easy-events') . ": <b>{delete_link}</b> " . __('Excerpt', 'wp-easy-events') . ": <b>{" . esc_html($ment) . "_excerpt}</b> " . __('Content', 'wp-easy-events') . ": <b>{" . esc_html($ment) . "_content}</b> " . __('Author Display Name', 'wp-easy-events') . ": <b>{" . esc_html($ment) . "_author_dispname}</b> " . __('Author NickName', 'wp-easy-events') . ": <b>{" . esc_html($ment) . "_author_nickname}</b> " . __('Author First Name','wp-easy-events') . ": <b>{" . esc_html($ment) . "_author_fname}</b> " . __('Author Last Name','wp-easy-events') . ": <b>{" . esc_html($ment) . "_author_lname}</b> " . __('Author Username','wp-easy-events') . ": <b>{" . esc_html($ment) . "_author_login}</b> " . __('Author Bio','wp-easy-events') . ": <b>{" . esc_html($ment) . "_author_bio}</b> " . __('Author Googleplus','wp-easy-events') . ": <b>{" . esc_html($ment) . "_author_googleplus}</b> " . __('Author Twitter','wp-easy-events') . ": <b>{" . esc_html($ment) . "_author_twitter}</b> ";
				$fields .= "<td>" . $builtin . "</td></tr>";
			}
		}
		else {
			$builtin = __('Title', 'wp-easy-events') . ": <b>{title}</b> " . __('Permalink', 'wp-easy-events') . ": <b>{permalink}</b> " . __('Edit Link', 'wp-easy-events') . ": <b>{edit_link}</b> " . __('Delete Link', 'wp-easy-events') . ": <b>{delete_link}</b> " . __('Excerpt', 'wp-easy-events') . ": <b>{excerpt}</b> " . __('Content', 'wp-easy-events') . ": <b>{content}</b> " . __('Author Display Name', 'wp-easy-events') . ": <b>{author_dispname}</b> " . __('Author NickName', 'wp-easy-events') . ": <b>{author_nickname}</b> " . __('Author First Name','wp-easy-events') . ": <b>{author_fname}</b> " . __('Author Last Name','wp-easy-events') . ": <b>{author_lname}</b> " . __('Author Username','wp-easy-events') . ": <b>{author_login}</b> " . __('Author Bio','wp-easy-events') . ": <b>{author_bio}</b> " . __('Author Googleplus','wp-easy-events') . ": <b>{author_googleplus}</b> " . __('Author Twitter','wp-easy-events') . ": <b>{author_twitter}</b> ";
			$fields .= "<tr><th>" . __('Builtin', 'wp-easy-events') . "</th>";
			$fields .= "<td>" . $builtin . "</td></tr>";
		}

		$site_params = __('Site Title','wp-easy-events') . " <b>{site_name}</b> " . __('Tagline','wp-easy-events') . " <b>{site_tagline}</b> " . __('Site Address','wp-easy-events') . " <b>{site_link}</b> ";
		$site_params = apply_filters('emd_notify_site_params',$site_params,$this->app,$ents);	
		$fields .= "<tr><th>" . __('Site', 'wp-easy-events') . "</th><td>" . $site_params . "</td></tr>";
		

		$fields .= "<tr><th>" . __('Globals', 'wp-easy-events') . "</th>
			<td>" . $globs . "</td>
			</tr>
			<tr><th>" . __('Attributes', 'wp-easy-events') . "</th>
			<td>" . $attrs . "</td>
			</tr>
			<tr><th>" . __('Taxonomies', 'wp-easy-events') . "</th>
			<td>" . $taxs . "</td>
			</tr>
			<tr><th>" . __('Relationships', 'wp-easy-events') . "</th>
			<td>" . $rels . "</td>
			</tr>
			<tr><th>" . __('Comments', 'wp-easy-events') . "</th>
			<td>" . $coms . "</td>
			</tr>
			</table>";
		$allowed_html = array(
			'table' => array('style'=>array()),
			'tr' => array('style'=>array()),
			'th' => array('style'=>array(), 'colspan' => array()),
			'td' => array('style'=>array()),
			'b' => array(),
		);
		echo wp_kses($fields,$allowed_html);
	}
	/**
	 * Display editor type on settings page
	 * @since WPAS 4.0
	 *
	 * @param array $args
	 *
	 * @return string $html
	 *
	 */
	public function notify_editor_callback($args) {
		ob_start();
		wp_editor($args['val'], $args['editor_id'], array(
			'tinymce' => false,
			'textarea_rows' => 10,
			'media_buttons' => true,
			'textarea_name' => $args['key'],
			'quicktags' => Array(
				'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,spell'
			)
		));
		echo ob_get_clean();
	}
	/**
	 * Add span with style for labels
	 * @since WPAS 4.0
	 *
	 * @param string $label
	 * @param string $info
	 * @param bool $padding
	 *
	 * @return string $html
	 *
	 */
	private function set_padding($label, $info = '', $padding = 0) {
		$html = '<span';
		if ($padding == 1) {
			$html.= ' style="padding-left:30px;"';
		}
		$html.= '>' . $label;
		if ($info != '') {
			$html.= ' <a title="' . esc_attr($info) . '" style="font-size:95%;" class="dashicons dashicons-info"></a>';
		}
		$html.= '</span>';
		return $html;
	}
	public static function display_settings_notify($app, $notify_init_list) {
		if (!empty($notify_init_list)) {
			global $title;
			echo '<div class="wrap"><h2>' . esc_html($title);
			echo '<a href="#" class="add-new-h2 upgrade-pro" style="padding:6px 10px;">' . esc_html('Import', 'wp-easy-events') . '</a>';
			echo '<a href="#" class="add-new-h2 upgrade-pro" style="padding:6px 10px;">' . esc_html('Export', 'wp-easy-events') . '</a>';
			echo '</h2>';
			echo '<p>' . esc_html__('Below is your notification list', 'wp-easy-events') . ':</p>';
			echo '<form method="post" action="options.php">';
			echo '<div id="notify-list" class="accordion-container"><ul class="outer-border">';
			settings_fields($app . '_notify_list');
			foreach ($notify_init_list as $knotify => $vnotify) {
				echo '<li id="' . esc_attr($knotify) . '" class="control-section accordion-section">
                                                <h3 class="accordion-section-title hndle" tabindex="0">' . esc_html($vnotify['label']) . '</h3>';
				echo '<div class="accordion-section-content"><div class="inside">';
				if (!empty($vnotify['desc'])) {
					echo '<p>' . esc_html($vnotify['desc']) . '</p>';
				}
				echo '<table class="form-table">';
				do_settings_fields('emd_setting_notify_' . $knotify, 'emd_setting_notify_' . $knotify);
				echo '</table>';
				echo '</div></div></li>';
			}
			submit_button();
			echo '</ul></div></form></div>';
		}
	}
}
