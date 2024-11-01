<?php
/**
 * admin actions
 *
 * @package     Emd_Calendar
 * @version 	1.0.0
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 4.7
 */
if (!defined('ABSPATH')) exit;


function emd_calendar_register_settings($app_name){
	register_setting($app_name . '_calendar_conf', $app_name . '_calendar_conf','emd_calendar_sanitize');
}
/**
 * Sanitize conf param
 * @since 1.0
 *
 * @param array $new
 *
 * @return array $new
 */
function emd_calendar_sanitize($new){
	return $new;
}

/**
 * Enqueue accordion and extension specific javascript
 * @since 1.0
 *
 * @param array $hook
 *
 */
function emd_calendar_admin_enq($app_name){
	wp_enqueue_script('accordion');
}

add_shortcode('emd_calendar', 'emd_calendar');

function emd_calendar($atts){
	$app_name = str_replace("-","_",$atts['app']);
	$cname = $atts['cname'];
	$calendar_conf_opt = get_option($app_name . '_calendar_conf');

	wp_enqueue_script('jquery');
	$calendar_vars['ajax_url'] = admin_url('admin-ajax.php');

	$cal_locales =  Array('en','af','ar','ar-dz','ar-kw','ar-ly','ar-ma','ar-sa','ar-tn','bg','ca','cs','da','de','de-at','de-ch','el','en-au','en-ca','en-gb','en-ie','en-nz','es','es-do','et','eu','fa','fi','fr','fr-ca','fr-ch','gl','he','hi','hr','hu','id','is','it','ja','kk','ko','lb','lt','lv','mk','ms','ms-my','nb','nl','nl-be','nn','pl','pt','pt-br','ro','ru','sk','sl','sr','sr-cyrl','sv','th','tr','uk','vi','zh-cn','zh-tw');
	$locale_code = strtolower(str_replace('_','-', get_locale()));
	if(in_array($locale_code,$cal_locales)){
		$calendar_vars['locale'] =  $locale_code;
	}
	else {
		$calendar_vars['locale'] = substr($locale_code, 0, 2 );
	}
	
	if(empty($calendar_conf_opt[$cname])){
		$has_calendar = get_option($app_name .'_has_calendar'); 
		$calendar_conf = $has_calendar[$cname];
	}
	else{
		$calendar_conf = $calendar_conf_opt[$cname];
	}

	$calendar_vars['ptype'] = $calendar_conf['entity'];
        $calendar_vars['cname'] = $cname;

	$views = isset($calendar_conf['views']) ? implode(",",$calendar_conf['views']) : 'month';
	$calendar_vars['header_left'] = 'prev,next,today';
	$calendar_vars['header_center'] = 'title';
	$calendar_vars['header_right'] = $views;
	$calendar_vars['default_view'] = isset($calendar_conf['default_view']) ? $calendar_conf['default_view'] : 'month';
	$calendar_vars['jui_theme'] = true;
	if(isset($calendar_conf['jui_theme']) && $calendar_conf['jui_theme'] != 'none'){
		$jui_theme = $calendar_conf['jui_theme'];
	}
	else {
                $jui_theme = 'smoothness';
	}
	$version = constant(strtoupper($app_name . '_VERSION'));
	wp_enqueue_style('theme-jui-css', constant(strtoupper($app_name) . '_PLUGIN_URL') . 'includes/emd-calendar/css/jquery-ui-themes-1.11.4/' . $jui_theme . '/jquery-ui.min.css','',$version);
	wp_enqueue_style('theme-css', constant(strtoupper($app_name) . '_PLUGIN_URL') . 'includes/emd-calendar/css/jquery-ui-themes-1.11.4/' . $jui_theme . '/theme.css','',$version);

	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('calendar-css', constant(strtoupper($app_name) . '_PLUGIN_URL') . 'includes/emd-calendar/css/fullcalendar.min.css','',$version);
	wp_enqueue_script('moment');
	wp_enqueue_script('fullcalendar-js',constant(strtoupper($app_name) . '_PLUGIN_URL') . 'includes/emd-calendar/js/fullcalendar.min.js','',$version);
	wp_enqueue_script('fullcalendar-locale-js',constant(strtoupper($app_name) . '_PLUGIN_URL') . 'includes/emd-calendar/js/locale-all.js','',$version);
	wp_enqueue_script('emd-calendar-js', constant(strtoupper($app_name) . '_PLUGIN_URL') . 'includes/emd-calendar/js/emd-calendar.js','',$version);
	wp_localize_script( 'emd-calendar-js', 'calendar_vars', $calendar_vars);
	wp_enqueue_style('emd-calendar-css', constant(strtoupper($app_name) . '_PLUGIN_URL') . 'includes/emd-calendar/css/emd-calendar.css','',$version);
	$html = "<div class='emd-container'>";
	if(!empty($cname)){
		$html .= "<input type='hidden' id='cname' name='cname' value='" . $cname . "'>";
		$html .= "<input type='hidden' id='ptype' name='ptype' value='" . $calendar_conf['entity'] . "'>";
	}
	if(!empty($app_name)){
		$html .= "<input type='hidden' id='app_name' name='app_name' value='" . $app_name . "'>";
	}

	$html .= "<div  id='emd-calendar-month-year'></div>";
	$html .= "<div id='emd-calendar-" . $cname . "'></div>";
	$html .= "<div class='emd-calendar-loading'></div>";
	$html.= "</div>";
	return $html;
}
add_action('wp_ajax_emd_calendar_ajax', 'emd_calendar_ajax');
add_action('wp_ajax_nopriv_emd_calendar_ajax', 'emd_calendar_ajax');
function emd_calendar_ajax() {
	$app_name = sanitize_text_field($_GET['app_name']);
	$cname = sanitize_text_field($_GET['cname']);
	$ptype = sanitize_text_field($_GET['ptype']);
	$calendar_conf = get_option($app_name . '_calendar_conf');

	$has_calendar = get_option($app_name .'_has_calendar'); 
	
	$args['post_type'] = $ptype;
	$args['post_status'] = 'publish';
	$args['posts_per_page'] = -1;
	//get meta query for start and end
	$args['meta_query'] = array(
		array(
			'key'     => $has_calendar[$cname]['start'],
			'value'   => sanitize_text_field($_GET['start']),
			'compare' => '>=',
			'type' => 'date',
		),
		array(
			'key' => $has_calendar[$cname]['end'],
			'value'   => sanitize_text_field($_GET['end']),
			'compare' => '<=',
			'type' => 'date',
		)
	);
	$pids = Array();
	$front_ents = emd_find_limitby('frontend', $app_name);
	if(!empty($front_ents) && in_array($ptype,$front_ents)){
		$pids = apply_filters('emd_limit_by', $pids, $app_name, $ptype, 'frontend');
	}
	if(!empty($pids)){
		$args['post__in'] = $pids;
	}
	
	$args = apply_filters('emd_calendar_ajax_args', $args, $app_name, $cname);
	$query = new WP_Query($args);
	$myposts = Array();
	$parr = Array();
	$myp = Array();
	if ($query->have_posts()) {
		$ent_attrs = get_option($app_name . '_attr_list');
		while ($query->have_posts()) {
			$query->the_post();
			$post = get_post(get_the_ID());
			$parr['id'] = get_the_ID();
			if($has_calendar[$cname]['title'] == 'blt_title'){
				$parr['title'] = $post->post_title;
			}
			else {
				$parr['title'] = emd_mb_meta($has_calendar[$cname]['title'],array(),get_the_ID());
			}
			
			$parr['url'] = get_permalink();
			$parr['start'] = emd_translate_date_format($ent_attrs[$ptype][$has_calendar[$cname]['start']],emd_mb_meta($has_calendar[$cname]['start'], array() , get_the_ID()));
			//'start' => '2015-10-12T20:00:00'
			$parr['end'] = emd_translate_date_format($ent_attrs[$ptype][$has_calendar[$cname]['end']],emd_mb_meta($has_calendar[$cname]['end'], array() , get_the_ID()));
			if($parr['start'] == $parr['end']){
				$parr['allDay'] = true;
			}
			else {
				$parr['allDay'] = false;
			}
			$parr['tooltip'] = sprintf(__('Click to go to %s page','wp-easy-events'), $parr['title']);
			$myp[] = $parr;
		}
	}
	wp_reset_query();
	echo wp_json_encode($myp);
	die();
}
