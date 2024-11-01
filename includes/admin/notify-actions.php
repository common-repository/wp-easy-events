<?php
/**
 * Notification Actions Functions
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       1.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Sends admin notifications
 *
 * @since WPAS 4.0
 *
 * @param string $app
 * @param array $mynotify
 * @param string $event
 * @param string $pid
 * @param array $rel_uniqs
 *
 */
if (!function_exists('emd_send_notification_admin')) {
	function emd_send_notification_admin($app, $mynotify, $pid) {
		if (!empty($mynotify['admin_msg'])) {
			$mynotify['admin_msg']['message'] = emd_parse_template_tags($app, $mynotify['admin_msg']['message'], $pid);
			$mynotify['admin_msg']['subject'] = emd_parse_template_tags($app, $mynotify['admin_msg']['subject'], $pid);
			$emd_email = new Emd_Notify_Email($mynotify['admin_msg']['from_name'],$mynotify['admin_msg']['from_email']);
			$emd_email->emd_send_email($mynotify['admin_msg']);

		}
		if (!empty($mynotify['user_msg'])) {
			$user_msg = $mynotify['user_msg'];
			$user_msg_arr = Array();
			$attr_list = get_option($app . '_attr_list');
			foreach ($mynotify['user_msg']['send_to'] as $send_to) {
				if ($send_to['active'] == 1 && !empty($send_to['attr'])){
					if(!empty($send_to['entity']) && $attr_list[$send_to['entity']][$send_to['attr']]['display_type'] == 'user'){
						$user_id = emd_mb_meta($send_to['attr'], '', $pid);
						$user_info = get_userdata($user_id);
						$sendto_email = $user_info->user_email;
					}
					else {
						$sendto_email = emd_mb_meta($send_to['attr'], '', $pid);
					}
					$user_msg_arr[$sendto_email]['message'] = emd_parse_template_tags($app, $user_msg['message'], $pid);
					$user_msg_arr[$sendto_email]['subject'] = emd_parse_template_tags($app, $user_msg['subject'], $pid);
				}
			}
			foreach($user_msg_arr as $msg_key => $msg_arr){
				$msg_arr['send_to'] = $msg_key;
				$msg_arr['from_name'] = (!empty($user_msg['from_name'])) ? $user_msg['from_name'] : '';
				$msg_arr['from_email'] = (!empty($user_msg['from_email'])) ? $user_msg['from_email'] : '';
				$msg_arr['reply_to'] = $user_msg['reply_to'];
				$msg_arr['cc'] = $user_msg['cc'];
				$msg_arr['bcc'] = $user_msg['bcc'];
				if(empty($emd_email)){
					$emd_email = new Emd_Notify_Email($msg_arr['from_name'],$msg_arr['from_email']);
				}
				$emd_email->emd_send_email($msg_arr);
			}
		}
	}
}
/**
 * Sends notification if there is active entity events
 *
 * @since WPAS 4.0
 *
 * @param string $app
 * @param int $pid
 * @param string $type
 * @param string $event
 *
 */
if (!function_exists('emd_check_notify_admin')) {
	function emd_check_notify_admin($app, $pid, $type, $event, $rel_uniqs = Array()) {
		$notify_list = get_option($app . "_notify_list");
		$mypost = get_post($pid);
		$ptype = $mypost->post_type;
		if (!empty($notify_list)) {
			foreach ($notify_list as $mynotify) {
				if ($mynotify['active'] == 1) {
					if ($type == 'entity' && $mynotify['level'] == $type && isset($mynotify['ev_' . $event]) && $mynotify['ev_' . $event] == 1 && $mynotify['entity'] == $ptype) {
						emd_send_notification_admin($app, $mynotify, $pid);
					}
				}
			}
		}
	}
}
add_action('emd_notify', 'emd_check_notify_admin', 10, 5);
add_action( 'login_redirect', 'emd_login_redirect', 10, 3);
/**
 * Check if login is from a notification email and forward it to redirect if a user is logged in
 *
 * @since WPAS 4.6
 *
 */
if (!function_exists('emd_login_redirect')) {
	function emd_login_redirect($redirect_to,$request, $user){
		if(preg_match('/fr_emd_notify/', $redirect_to)){
			$redirect_to = preg_replace('/fr_emd_notify.*/','',$redirect_to);
			$my_user = wp_get_current_user();
			if(!empty($my_user) && $my_user->ID != 0){
				global $user;
				$user = $my_user;
				return $redirect_to;
			}
			else {
				return $redirect_to;
			}
		}
		return $redirect_to;
	}
}
if (!class_exists('Emd_Notify_Email')) {
	class Emd_Notify_Email {

		private $from_email;

		private $from_name;

		public function __construct($from_name,$from_email) {
			$this->from_email = $from_email;
			$this->from_name = $from_name;
		}
		/**
		 * Use wp_mail to send notifications
		 *
		 * @since WPAS 4.0
		 *
		 * @param array $conf_arr
		 *
		 */
		public function emd_send_email($conf_arr) {
			if(!empty($conf_arr['send_to'])){
				if ($conf_arr['from_name'] != '') {
					$from_name = $conf_arr['from_name'];
					add_filter('wp_mail_from_name', array($this,'emd_set_from_name'));
				}
				else {
					$from_name = get_bloginfo('name');
				}
				if ($conf_arr['from_email'] != '') {
					$from_email = $conf_arr['from_email'];
					add_filter('wp_mail_from', array($this,'emd_set_from_email'));
				}
				else {
					$from_email = get_option('admin_email');
				}
				$headers = "From: " . stripslashes_deep(html_entity_decode($from_name, ENT_COMPAT, 'UTF-8')) . " <" . $from_email . ">\r\n";
				$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
				if ($conf_arr['reply_to'] != '') {
					$headers.= "Reply-To: " . $conf_arr['reply_to'] . "\r\n";
				} else {
					$headers.= "Reply-To: " . $from_email . "\r\n";
				}
				if ($conf_arr['cc'] != '') {
					$headers.= "Cc: " . $conf_arr['cc'] . "\r\n";
				}
				if ($conf_arr['bcc'] != '') {
					$headers.= "Bcc: " . $conf_arr['bcc'] . "\r\n";
				}
				wp_mail($conf_arr['send_to'], $conf_arr['subject'], $conf_arr['message'], $headers);
				remove_filter('wp_mail_from_name', array($this,'emd_set_from_name'));
				remove_filter('wp_mail_from', array($this,'emd_set_from_email'));

			}
		}
		public function emd_set_from_name(){
			return $this->from_name;
		}
		public function emd_set_from_email(){
			return $this->from_email;
		}
	}
}
