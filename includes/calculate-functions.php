<?php
/**
 * Calculate formulas
 * @package WP_EASY_EVENTS
 * @since WPAS 4.6
 */
if (!defined('ABSPATH')) exit;
add_action('wp_ajax_wp_easy_events_emd_calc_formula', 'wp_easy_events_emd_calc_formula');
add_action('wp_ajax_nopriv_wp_easy_events_emd_calc_formula', 'wp_easy_events_emd_calc_formula');
function wp_easy_events_emd_calc_formula() {
	require_once WP_EASY_EVENTS_PLUGIN_DIR . 'assets/ext/calculate/calculate.php';
	$result = '';
	$func = sanitize_text_field($_GET['function']);
	switch ($func) {
		case 'EMD_VENUE_FULLADDRESS':
			$A17 = sanitize_text_field($_GET["params"][1]);
			$A18 = sanitize_text_field($_GET["params"][2]);
			$A19 = sanitize_text_field($_GET["params"][3]);
			$A20 = sanitize_text_field($_GET["params"][4]);
			$A21 = sanitize_text_field($_GET["params"][5]);
			if (isset($A17) && isset($A18) && isset($A19) && isset($A21)) {
				$result = emd_calculate_concat(array(
					$A17,
					", ",
					emd_calculate_if($A18, $A18, "") ,
					" ",
					$A19,
					" ",
					emd_calculate_if($A20, $A20, "") ,
					" ",
					$A21
				));
			}
		break;
	}
	echo $result;
	die();
}