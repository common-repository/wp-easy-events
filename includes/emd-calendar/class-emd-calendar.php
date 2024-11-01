<?php
/**
 * Calendar Class
 *
 * @package     
 * @copyright   Copyright (c) 2014,  Emarket Design
*  @since       WPAS 4.7
 */
if (!defined('ABSPATH')) exit;
/**
 * Emd_Calendar Class
 *
 * @since 1.0
 */
class Emd_Calendar {
	var $app = "";
	/**
	 * Instantiate calendar class 
	 * Add action to display settings
	 * @since 1.0
	 *
	 * @param string $app
	 *
	 */
	public function __construct($app) {
		add_action('emd_display_settings_calendar', array(
					$this,
					'display_settings'
					));
		$this->app = $app;
	}
	/**
	 * Display calendar page
	 * @since 1.0
	 *
	 * @param string $app
	 *
	 * @return html
	 */
	public function display_settings($app) {
		if($app != $this->app){
			return;
		}
		global $title;

		$calendar_conf = get_option($this->app . '_calendar_conf');
		$settings_errors = get_settings_errors($this->app . '_calendar_conf');
		?>
			<div class="wrap">
			<h2><?php echo esc_html($title);?></h2>
			<?php if ( isset( $_GET['settings-updated'] )  && empty($settings_errors)){ ?>
				<div class='updated'>
					<p><?php esc_html_e( 'Your settings have been saved.', 'wp-easy-events'); ?></p>
					</div>
					<?php }else {
					settings_errors($this->app . '_calendar_conf');
				} ?>

			<form id="calendar_conf" method="post" action="options.php">
			<input type='hidden' id='<?php echo esc_attr($this->app) ?>_calendar_conf_app_name' name='<?php echo esc_attr($this->app) ?>_calendar_conf[app_name]' value='<?php echo esc_attr($this->app); ?>'>
			<?php 
			settings_fields($this->app . '_calendar_conf');  
		$calendar_conf = get_option($this->app . '_calendar_conf');

		$this->display_calendars($calendar_conf);
		?>
			</div><!-- .wrap -->
			<?php
	}
	/**
	 * Display calendars in accordion which has calendar conf
	 * @since 1.0
	 *
	 * @param array $calendar_conf
	 *
	 * @return html 
	 */
	private function display_calendars($calendar_conf){
		$has_calendar = get_option($this->app .'_has_calendar'); ?>
		<div id="tab-entity" class="tab-content">	
		<p><?php esc_html_e('Define calendar options by clicking on each entity name.','wp-easy-events'); ?></p>	
		<?php		
		if (!empty($has_calendar)) { ?>
			<div id="calendar-entity-list" class="accordion-container">
				<ul class="outer-border" style="margin-top:20px;">
				<?php foreach ($has_calendar as $key_cal => $val_cal) {
					echo '<li id="' . esc_attr($key_cal) . '" class="control-section accordion-section">
						<h3 class="accordion-section-title hndle" tabindex="0">' . esc_html($val_cal['label']) . '</h3>';
					echo '<div class="accordion-section-content"><div class="inside">';
					$this->conn_conf($calendar_conf,$key_cal,$val_cal);
					echo '</table>';
					echo '</div></div></li>';
				}
			echo '</ul>';
			submit_button();
			echo '</div>';
		}
		echo '</div></form>';
	}
	/**
	 * Display settings for each calendar connection
	 * @since 1.0
	 *
	 * @param array $calendar_conf
	 * @param string $key_cal
	 * @param array $val_cal
	 *
	 * @return html 
	 */
	private function conn_conf($calendar_conf,$key_cal,$val_cal){
		if(empty($calendar_conf[$key_cal]['entity'])){
			$entity = $val_cal['entity'];
		}
		else {
			$entity = $calendar_conf[$key_cal]['entity'];
		}
		?>
			<input type='hidden' id='<?php echo esc_attr($this->app) ?>_calendar_conf_<?php echo esc_attr($key_cal);?>_entity' name='<?php echo esc_attr($this->app) ?>_calendar_conf[<?php echo esc_attr($key_cal); ?>][entity]' value='<?php echo esc_attr($val_cal['entity']); ?>'>
			<table class="form-table">
			<tbody>
			<tr>
			<th scope="row">
			<label for="views">
			<?php esc_html_e('Views', 'wp-easy-events'); ?>
			</label>
			</th>
			<td>
			<input id='<?php echo esc_attr($this->app) ?>_calendar_conf_<?php echo esc_attr($key_cal);?>_views' name='<?php echo esc_attr($this->app) ?>_calendar_conf[<?php echo esc_attr($key_cal); ?>][views][]' type='checkbox' value="month" <?php echo (isset($calendar_conf[$key_cal]['views']) && in_array('month',$calendar_conf[$key_cal]['views'])) ? " checked" : "" ?>></input> <?php esc_html_e('Month','wp-easy-events'); ?><br>
			
			<input id='<?php echo esc_attr($this->app) ?>_calendar_conf_<?php echo esc_attr($key_cal);?>_views' name='<?php echo esc_attr($this->app) ?>_calendar_conf[<?php echo esc_attr($key_cal); ?>][views][]' type='checkbox' value="basicWeek" <?php echo (isset($calendar_conf[$key_cal]['views']) && in_array('basicWeek',$calendar_conf[$key_cal]['views'])) ? " checked" : "" ?>></input> <?php esc_html_e('Basic Week','wp-easy-events'); ?><br>
			<input id='<?php echo esc_attr($this->app) ?>_calendar_conf_<?php echo esc_attr($key_cal);?>_views' name='<?php echo esc_attr($this->app) ?>_calendar_conf[<?php echo esc_attr($key_cal); ?>][views][]' type='checkbox' value="basicDay" <?php echo (isset($calendar_conf[$key_cal]['views']) && in_array('basicDay',$calendar_conf[$key_cal]['views'])) ? " checked" : "" ?>></input> <?php esc_html_e('Basic Day','wp-easy-events'); ?><br>
			<input id='<?php echo esc_attr($this->app) ?>_calendar_conf_<?php echo esc_attr($key_cal);?>_views' name='<?php echo esc_attr($this->app) ?>_calendar_conf[<?php echo esc_attr($key_cal); ?>][views][]' type='checkbox' value="agendaWeek" <?php echo (isset($calendar_conf[$key_cal]['views']) && in_array('agendaWeek',$calendar_conf[$key_cal]['views'])) ? " checked" : "" ?>></input> <?php esc_html_e('Agenda Week','wp-easy-events'); ?><br>
			<input id='<?php echo esc_attr($this->app) ?>_calendar_conf_<?php echo esc_attr($key_cal);?>_views' name='<?php echo esc_attr($this->app) ?>_calendar_conf[<?php echo esc_attr($key_cal); ?>][views][]' type='checkbox' value="agendaDay" <?php echo (isset($calendar_conf[$key_cal]['views']) && in_array('agendaDay',$calendar_conf[$key_cal]['views'])) ? " checked" : "" ?>></input> <?php esc_html_e('Agenda Day','wp-easy-events'); ?><br>
			<p class="description"><?php esc_html_e( 'Views are ways of displaying days and events.','wp-easy-events' ); ?></p>
			</td>
			</tr>
			<tr>
			<th scope="row">
			<label for="default_view">
			<?php esc_html_e('Default View', 'wp-easy-events'); ?>
			</label>
			</th>
			<td>
			<select id='<?php echo esc_attr($this->app) ?>_calendar_conf_<?php echo esc_attr($key_cal);?>default_view' name='<?php echo esc_attr($this->app) ?>_calendar_conf[<?php echo esc_attr($key_cal); ?>][default_view]'>
			<option value="month" <?php echo (isset($calendar_conf[$key_cal]['default_view']) && $calendar_conf[$key_cal]['default_view'] == 'month') ? "selected='selected' " : "" ?>><?php esc_html_e('Month','wp-easy-events'); ?></option>
			<option value="basicWeek" <?php echo (isset($calendar_conf[$key_cal]['default_view']) && $calendar_conf[$key_cal]['default_view'] == 'basicWeek') ? "selected='selected' " : "" ?>><?php esc_html_e('Basic Week','wp-easy-events'); ?></option>
			<option value="basicDay" <?php echo (isset($calendar_conf[$key_cal]['default_view']) && $calendar_conf[$key_cal]['default_view'] == 'basicDay') ? "selected='selected' " : "" ?>><?php esc_html_e('Basic Day','wp-easy-events'); ?></option>
			<option value="agendaWeek" <?php echo (isset($calendar_conf[$key_cal]['default_view']) && $calendar_conf[$key_cal]['default_view'] == 'agendaWeek') ? "selected='selected' " : "" ?>><?php esc_html_e('Agenda Week','wp-easy-events'); ?></option>
			<option value="agendaDay" <?php echo (isset($calendar_conf[$key_cal]['default_view']) && $calendar_conf[$key_cal]['default_view'] == 'agendaDay') ? "selected='selected' " : "" ?>><?php esc_html_e('Agenda Day','wp-easy-events'); ?></option>
			</select>
			</td>
			</tr>
			<?php 
			$header_options = Array('title' => __('Title','wp-easy-events'),
						'prev' => __('Previous','wp-easy-events'),
						'next' => __('Next','wp-easy-events'),
						'prevYear' => __('Previous Year','wp-easy-events'),
						'nextYear' => __('Next Year','wp-easy-events'),
						'today' => __('Today','wp-easy-events'),
						'views' => __('Views','wp-easy-events')
					);
			?>
			<tr>
			<th scope="row">
			<label for="jui_theme">
			<?php esc_html_e('jQuery UI Theme', 'wp-easy-events'); ?>
			</label>
			</th>
			<td>
			<select id='<?php echo esc_attr($this->app) ?>_calendar_conf_<?php echo esc_attr($key_cal);?>jui_theme' name='<?php echo esc_attr($this->app) ?>_calendar_conf[<?php echo esc_attr($key_cal); ?>][jui_theme]'>
			<?php
			if(isset($calendar_conf[$key_cal]['jui_theme']) && $calendar_conf[$key_cal]['jui_theme'] == 'none'){
				$calendar_conf[$key_cal]['jui_theme'] = 'smoothness';
			}
			$themes= Array('smoothness' => __("Smoothness",'wp-easy-events'),
					'ui-lightness' => __("UI lightness",'wp-easy-events'),
					'ui-darkness' => __("UI darkness",'wp-easy-events'),
					'start' => __("Start",'wp-easy-events'),
					'redmond' => __("Redmond",'wp-easy-events'),
					'sunny' => __("Sunny",'wp-easy-events'),
					'overcast' => __("Overcast",'wp-easy-events'),
					'le-frog' => __("Le Frog",'wp-easy-events'),
					'flick' => __("Flick",'wp-easy-events'),
					'pepper-grinder' => __("Pepper Grinder",'wp-easy-events'),
					'eggplant' => __("Eggplant",'wp-easy-events'),
					'dark-hive' => __("Dark Hive",'wp-easy-events'),
					'cupertino' => __("Cupertino",'wp-easy-events'),
					'south-street' => __("South Street",'wp-easy-events'),
					'blitzer' => __("Blitzer",'wp-easy-events'),
					'humanity' => __("Humanity",'wp-easy-events'),
					'hot-sneaks' => __("Hot Sneaks",'wp-easy-events'),
					'excite-bike' => __("Excite Bike",'wp-easy-events'),
					'vader' => __("Vader",'wp-easy-events'),
					'dot-luv' => __("Dot Luv",'wp-easy-events'),
					'mint-choc' => __("Mint Choc",'wp-easy-events'),
					'black-tie' => __("Black Tie",'wp-easy-events'),
					'trontastic' => __("Trontastic",'wp-easy-events'),
					'swanky-purse' => __("Swanky Purse",'wp-easy-events')
				);
			foreach($themes as $ktheme => $vtheme){
				echo '<option value="' .  esc_attr($ktheme) . '" ';
				if(isset($calendar_conf[$key_cal]['jui_theme']) && $calendar_conf[$key_cal]['jui_theme'] == $ktheme){
					echo  "selected='selected' ";
				}
				echo '>' . esc_html($vtheme) . '</option>';
			}
			?>
			</select>
			<p class="description"><?php esc_html_e( 'Enables/disables use of jQuery UI theming.','wp-easy-events' ); ?></p>
			</td>
			</tr>
			</tbody>
			</table>
			<?php
	}
}
