<?php
/**
 * Form Settings Functions
 *
 */
if (!defined('ABSPATH')) exit;

function emd_form_builder_lite_settings($type,$app,$form_id=0,$result = ''){
	if($type == 'edit'){
		$myform = get_post($form_id);
		$fcontent = json_decode($myform->post_content,true);
		$fcontent['title'] = $myform->post_title;
		$fcontent['settings']['type'] = $fcontent['type'];
	}
	elseif($type == 'new'){
		$fcontent = Array();
	}
	$tabs['general'] = __('General', 'wp-easy-events');
	$tabs['display'] = __('Display', 'wp-easy-events');
	$tabs['submit'] = __('Submissions', 'wp-easy-events');
	if(empty($fcontent['type']) || (!empty($fcontent['type']) && $fcontent['type'] != 'search')){
		$tabs['confirm'] = __('Confirmations', 'wp-easy-events');
	}
	$tabs['schedule'] = __('Scheduling', 'wp-easy-events');
	$tabs['antispam'] = __('Anti Spam', 'wp-easy-events');
	if(empty($fcontent['type']) || (!empty($fcontent['type']) && $fcontent['type'] != 'search')){
		$tabs['page'] = __('Pages', 'wp-easy-events');
	}
	else {
		//this will be added when we add ability to create new search forms
		$tabs['search'] = __('Search','wp-easy-events');
	}
	$tabs['code'] = __('Code', 'wp-easy-events');
	$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
	echo '<div class="wrap">';
	echo '<form name="emd-form-settings" method="post">';
	echo '<input type="hidden" name="submit_settings" value="1">';
	wp_nonce_field('emd_form_settings', 'emd_form_settings_nonce', false, true);
	if($type == 'edit'){
		echo '<input type="hidden" name="id" value="' . esc_attr($form_id) . '">';
		echo '<input type="hidden" name="stype" value="edit">';
		echo '<input type="hidden" name="app" value="' . esc_attr($app) . '">';
		echo '<h2>' . esc_html__('Form Settings:','wp-easy-events') . ' ' . esc_html($myform->post_title) . '</h2>';
	}
	else {
		echo '<input type="hidden" name="stype" value="new">';
		echo '<input type="hidden" name="app" value="' . esc_attr($app) . '">';
		echo '<h2>' . esc_html__('Form Settings:','wp-easy-events')  . '</h2>';
	}	
	if($result == 'error'){
		echo '<div class="is-dismissible notice error"><p><strong>' . esc_html__('Error when saving.','wp-easy-events') . '</strong></p></div>';
	}
	elseif($result == 'success'){
		echo '<div class="updated is-dismissible notice"><p><strong>' . esc_html__('Saved successfully.','wp-easy-events') . '</strong></p></div>';
	}
	echo '<h2 class="nav-tab-wrapper">';
	foreach ($tabs as $ktab => $mytab) {
		$turl = remove_query_arg(array('action','_wpnonce'));
		$tab_url[$ktab] = esc_url(add_query_arg(array(
						'tab' => $ktab
						),$turl));
		$active = "";
		$pro = "";
		if ($active_tab == $ktab) {
			$active = "nav-tab-active";
		}
		if(in_array($ktab,Array('display','submit','schedule','page','code'))){
			$pro = " upgrade-pro";
		}
		echo '<a href="' . esc_url($tab_url[$ktab]) . '" class="nav-tab ' . esc_attr($active) . esc_attr($pro) . '" id="nav-' . esc_attr($ktab) . '">' . esc_html($mytab) . '</a>';
	}
	echo '</h2>';
	foreach(array_keys($tabs) as $tab){
		emd_form_builder_lite_tab($type,$tab,$app,$active_tab,$fcontent);
	}
	echo submit_button(); 
	echo '</form>';
	echo '</div>';
}
function emd_form_builder_lite_tab($type,$mytab,$app,$active_tab,$fcontent){
	switch($mytab){
		case 'general':
			$login_reg_options = Array('none' => __('None','wp-easy-events'),
				'both' => __('Registration and Login Forms','wp-easy-events'),
				'login' => __('Login Form only','wp-easy-events'));
			$type_options = Array('submit' => __('Submit','wp-easy-events'),
				'search' => __('Search','wp-easy-events')
			);
			$disabled = false;
			if((!empty($fcontent['source']) && $fcontent['source'] == 'plugin') || $type != 'new'){
				$disabled = true;
			}
			$ent_list = get_option($app . '_ent_list', Array());
			foreach($ent_list as $kent => $myent){
				$ent_options[$kent] = $myent['label'];
			}	
			$tab_fields = Array('title' => Array('label' => __('Name','wp-easy-events'), 'type' => 'text', 'required' => true),
			'type' => Array('label' => __('Type','wp-easy-events'), 'type' => 'select', 'options' => $type_options, 'disabled' => $disabled, 'required' => true),
			'entity' => Array('label' => __('Entity','wp-easy-events'), 'type' => 'select', 'options' => $ent_options, 'disabled' => $disabled, 'required' => true),
			'login_reg' => Array('label' => __('Show Register / Login Form','wp-easy-events'), 'type' => 'select', 'options' => $login_reg_options,
					'desc'=>__('Displays or hides registration and login forms on this form for non-logged-in users.','wp-easy-events')),
			'noaccess_msg' => Array('label' => __('No Access Message','wp-easy-events'), 'type' => 'textarea', 'default' => __('You are not allowed to access to this area. Please contact the site administrator.','wp-easy-events')),
			);	
			break;
		case 'display':
			$device_options = Array('desktops' => __("Desktops","wp-easy-events"),
					'phones' => __("Phones","wp-easy-events"),
					'tablets' => __("Tablets","wp-easy-events"),
					'large_desktops' => __("Large Desktops","wp-easy-events")
			);
			$placement_options = Array('top' => __("Top","wp-easy-events"),
						'left' => __("Left","wp-easy-events"),
						'inside' => __('Inside','wp-easy-events'),
			);
			$size_options = Array('medium' => __("Medium","wp-easy-events"),
						'small' => __("Small","wp-easy-events"),
						'large' => __('Large','wp-easy-events'),
			);
			$tab_fields = Array('targeted_device' => Array('label' => __('Targeted Device','wp-easy-events'), 'type' => 'select', 'options' => $device_options),
					'label_position' => Array('label' => __('Label Placement','wp-easy-events'), 'type' => 'select', 'options' => $placement_options,
						'desc' => __("Sets the field label position relative to the field input location. Options are Top,Left or Inside. Pick your label placement based on the space you have available for the form. Min 680px required for inside/top label placement with 3 column layout. If you enabled operators in your search form, you will need more space for multi-layout designs. You can always adjust the width css element of your form container when needed. Enabling operators will give access to all of your data so limiting access by role may always be a good idea.","wp-easy-events")
						),
					'element_size' => Array('label' => __('Element Size','wp-easy-events'), 'type' => 'select', 'options' => $size_options),		
					'display_inline' => Array('label' => __('Display Radios and Checkboxes Inline','wp-easy-events'), 'type'=>'checkbox'),
					'fill_usermap' => Array('label' => __('Do not autofill Logged-in User Information','wp-easy-events'), 'type'=>'checkbox'),
					'form_class' => Array('label' => __('Form Class','wp-easy-events'), 'type'=>'text'),
				);		
			break;
		case 'submit':
			$status_options = Array('publish' => __('Publish','wp-easy-events'),
						'draft' => __('Draft','wp-easy-events'),
						'future' => __('Future','wp-easy-events'),
						'private' => __('Private','wp-easy-events'),
						'trash' => __('Trash','wp-easy-events')
			);
			$button_options = Array('btn-standard' =>  __("Standard (White - #FFFFF)","wp-easy-events"),
						'btn-primary' => __("Primary (Blue - #006DCC)","wp-easy-events"),
						'btn-info' => __("Info (Light Blue - #49AFCD)","wp-easy-events"),
						'btn-success' => __("Success (Green - #5BB75B)","wp-easy-events"),
						'btn-warning' => __("Warning (Orange - #FAA732)","wp-easy-events"),
						'btn-danger' => __("Danger (Red - #DA4F49)","wp-easy-events"),
						'btn-inverse' => __("Inverse (Black - #363636)","wp-easy-events"),
						'btn-link' => __("Link (Blue -  #0088CC)","wp-easy-events"),
						'btn-custom' => __("Custom","wp-easy-events"),
			);
			$button_size_options = Array('btn-std' =>  __("Standard","wp-easy-events"),
							'btn-xlarge' => __("XLarge","wp-easy-events"),
							'btn-large' => __("Large","wp-easy-events"),
							'btn-small' => __("Small","wp-easy-events"),
							'btn-mini' => __("Mini","wp-easy-events"),
			);
			$fa_size_options = Array('' => __('Standard','wp-easy-events'),
					'fa-lg' => __('Large','wp-easy-events'),
					'fa-2x' => __('2x','wp-easy-events'),
					'fa-3x' => __('3x','wp-easy-events'),
					'fa-4x' => __('4x','wp-easy-events'),
					'fa-5x' => __('5x','wp-easy-events'),
					);
			$fa_pos_options = Array('left' => __('Left','wp-easy-events'),
						'right' => __('Right','wp-easy-events'),
					);
			if(empty($fcontent['type']) || (!empty($fcontent['type']) && $fcontent['type'] != 'search')){
				$tab_fields = Array('disable_submit' => Array('label' => 'Disable Submit Action', 'type' => 'checkbox'),
					'submit_status' => Array('label' => 'Submit Status', 'type' => 'select', 'depend' => 'settings_disable_submit_1','options' => $status_options),		
					'visitor_submit_status' => Array('label' => 'Visitor Submit Status', 'type' => 'select', 'depend' => 'settings_disable_submit_1','options' => $status_options),		
					'submit_button_type' => Array('label' => 'Submit Button Type', 'type' => 'select','options' => $button_options),		
					'submit_button_label' => Array('label' => 'Submit Button Label', 'type' => 'text'),		
					'submit_button_class' => Array('label' => 'Submit Button Class', 'type' => 'text'),		
					'submit_button_block' => Array('label' => 'Create Block Level Button', 'type'=>'checkbox'),
					'submit_button_size' => Array('label' => 'Submit Button Size', 'type' => 'select','options' => $button_size_options),
					'submit_button_fa' => Array('label' => 'Submit Button Icon Class', 'type' => 'text', 'size'=> 'medium'),
					'submit_button_fa_size' => Array('label' => 'Submit Button Icon Size', 'type' => 'select', 'options' => $fa_size_options),
					'submit_button_fa_pos' => Array('label' => 'Submit Button Icon Position', 'type' => 'select', 'options' => $fa_pos_options),
					'disable_after' => Array('label' => 'Disable After', 'type' => 'text', 'size'=> 'mini', 'depend' => 'settings_disable_submit_1'),		
				);
			}
			else {
				$tab_fields = Array('submit_button_type' => Array('label' => 'Submit Button Type', 'type' => 'select','options' => $button_options),		
					'submit_button_label' => Array('label' => 'Submit Button Label', 'type' => 'text'),		
					'submit_button_block' => Array('label' => 'Create Block Level Button', 'type'=>'checkbox'),
					'submit_button_size' => Array('label' => 'Submit Button Size', 'type' => 'select','options' => $button_size_options),
					'submit_button_fa' => Array('label' => 'Submit Button Icon Class', 'type' => 'text', 'size'=> 'small'),
					'submit_button_fa_size' => Array('label' => 'Submit Button Icon Size', 'type' => 'select', 'options' => $fa_size_options),
					'submit_button_fa_pos' => Array('label' => 'Submit Button Icon Position', 'type' => 'select', 'options' => $fa_pos_options),
				);
			}
			break;
		case 'confirm':
			$confirm_options = Array('text' => __('Show text','wp-easy-events'),
						'redirect' => __('Redirect','wp-easy-events'),
						'form' => __('Go to another form','wp-easy-events')
					);
			$after_options = Array('show' => __('Show Form','wp-easy-events'),
						'hide' => __('Hide Form','wp-easy-events'),
			);
			$forms = get_posts(Array('post_type'=>'emd_form','posts_per_page'=>-1,'post_status'=>'publish'));
			$form_options = Array();
			if(!empty($forms)){
				foreach($forms as $myform){
					$mycont = json_decode($myform->post_content,true);
					if((empty($fcontent['name']) || (!empty($fcontent['name']) && $mycont['name'] != $fcontent['name'])) && $mycont['type'] == 'submit'){
						$form_options[$myform->ID] = $myform->post_title;
					}
				}
			}
			$tab_fields = Array('confirm_method' => Array('label' => 'Confirmation Method', 'type' => 'select', 'options' => $confirm_options, 'upgrade' => true, 'disabled' => true),
					'confirm_form' => Array('label' => 'Next Form', 'type' => 'select', 'depend' => 'settings_confirm_method_form', 'options' => $form_options, 'upgrade' => true, 'disabled' => true),
					'confirm_url' => Array('label' => 'Redirect URL', 'type' => 'text', 'depend' => 'settings_confirm_method_redirect', 'upgrade' => true, 'disabled' => true),
					'enable_ajax' => Array('label' => 'Enable Ajax', 'type' => 'checkbox', 'depend' => 'settings_confirm_method_text', 'upgrade' => true, 'disabled' => true),
					'after_submit' => Array('label' => 'After Submit', 'type' => 'select', 'depend' => 'settings_confirm_method_text', 'options' => $after_options, 'upgrade' => true, 'disabled' => true),
					'success_msg' => Array('label' => 'Success Message', 'type' => 'textarea', 'depend' => 'settings_confirm_method_text','default'=> __('Thanks for your submission.','wp-easy-events')),
					'error_msg' => Array('label' => 'Error Message', 'type' => 'textarea', 'depend' => 'settings_confirm_method_text','default'=> __('There has been an error when submitting your entry. Please contact the site administrator.')),
					
			);
			break;
		case 'schedule':
			$tab_fields = Array('schedule_start' => Array('label' => 'Start Datetime', 'type' => 'text', 'size' => 'small'),
				'schedule_end' => Array('label' => 'End Datetime', 'type' => 'text', 'size' => 'small'),
			);
			break;
		case 'antispam':
			$captcha_options = Array('never_show' => __('Never Show','wp-easy-events'),
						'show_always' => __('Always Show','wp-easy-events'),
						'show_to_visitors' => __('Visitors Only','wp-easy-events'),
					);
			$tab_fields = Array('honeypot' => Array('label' => 'HoneyPot', 'type' => 'checkbox'),
					'captcha' => Array('label' => 'Show Captcha', 'type' => 'radio','options' => $captcha_options, 'upgrade' => true, 'disabled' => true),
					'captcha_site_key' => Array('label' => 'reCAPTCHA v3 Site Key', 'type' => 'text', 'depend' => 'settings_captcha_show_always' , 'depend2' => 'settings_captcha_show_to_visitors', 'upgrade' => true, 'disabled' => true),
					'captcha_secret_key' => Array('label' => 'reCAPTCHA v3 Secret Key', 'type' => 'text', 'depend' => 'settings_captcha_show_always', 'depend2' => 'settings_captcha_show_to_visitors', 'upgrade' => true, 'disabled' => true),
			);
			break;
		case 'page':
			$wizard_options = Array('default' => __('Tabs','wp-easy-events'),
						'arrows' => __('Arrows','wp-easy-events'),
						'circles' => __('Circles','wp-easy-events'),
						'dots' => __('Dots','wp-easy-events'),
					);
			$wizard_toolbar_options = Array('bottom' => __('Bottom','wp-easy-events'),
							'top' => __('Top','wp-easy-events'),
							'both' => __('Both','wp-easy-events')
						);
			$transition_options = Array('none' => __('None','wp-easy-events'),
						'slide' => __('Slide','wp-easy-events'),
						'fade' => __('Fade','wp-easy-events')
					);
			$status_options = Array('publish' => __('Publish','wp-easy-events'),
						'draft' => __('Draft','wp-easy-events'),
						'future' => __('Future','wp-easy-events'),
						'private' => __('Private','wp-easy-events'),
						'trash' => __('Trash','wp-easy-events')
			);
			$tab_fields = Array('wizard_style' => Array('label' => __('Wizard Style','wp-easy-events'), 'type' => 'select','options' => $wizard_options),
					'wizard_vertical' => Array('label' => __('Display Vertical Wizard Steps','wp-easy-events'), 'type' => 'checkbox'),
					'wizard_toolbar' => Array('label' => __('Wizard Button Toolbar','wp-easy-events'), 'type' => 'select','options' => $wizard_toolbar_options),
					'wizard_cancel_url' => Array('label' => 'Cancel Button Link', 'type' => 'text'),
					'wizard_trans_effect' => Array('label' => __('Wizard Transition Effect','wp-easy-events'), 'type' => 'select','options' => $transition_options),
					'wizard_trans_speed' => Array('label' => 'Wizard Transition Speed', 'type' => 'text', 'size' => 'small', 'default' => 400),
					'wizard_save_step' => Array('label' => __('Save Wizard Step Data','wp-easy-events'),'type'=>'checkbox'),
					'step_submit_status' => Array('label' => 'Wizard Step Submit Status', 'type' => 'select', 'depend' => 'settings_wizard_save_step_1','options' => $status_options),		
					'step_visitor_submit_status' => Array('label' => 'Wizard Step Visitor Submit Status', 'type' => 'select', 'depend' => 'settings_wizard_save_step_1','options' => $status_options),		
			);
			break;
		case 'search':
			$attr_list = get_option($fcontent['app'] . '_attr_list',Array());
			$txn_list = get_option($fcontent['app'] . '_tax_list', Array());
			$rel_list = get_option($fcontent['app'] . '_rel_list', Array());
			$ent_list = get_option($fcontent['app'] . '_ent_list',Array());
			$blt_fields = Array('blt_title','blt_content','blt_excerpt');
			$result_options = Array();
			foreach($blt_fields as $myblt){
				if(!empty($ent_list[$fcontent['entity']]['req_blt'][$myblt])){
					$result_options[$myblt] = $ent_list[$fcontent['entity']]['req_blt'][$myblt]['msg'];
				}
				elseif(!empty($ent_list[$fcontent['entity']]['blt_list'][$myblt])){
					$result_options[$myblt] = $ent_list[$fcontent['entity']]['blt_list'][$myblt];
				}	
			}
			if(!empty($attr_list[$fcontent['entity']])){
				foreach($attr_list[$fcontent['entity']] as $kattr => $vattr){
					if(!preg_match('/^wpas_/',$kattr)){
						$result_options[$kattr] = $vattr['label'];
					}
				}
			}
			if(!empty($txn_list[$fcontent['entity']])){
				foreach($txn_list[$fcontent['entity']] as $ktxn => $vtxn){
					$result_options[$ktxn] = $vtxn['label'];
				}
			}
			if(!empty($rel_list)){
				foreach($rel_list as $krel => $vrel){
					if($fcontent['entity'] == $vrel['from']){
						$result_options[$krel] = $vrel['from_title'];
					}
					elseif($fcontent['entity'] == $vrel['to']){
						$result_options[$krel] = $vrel['to_title'];
					}
				}
			}
			if(!empty($fcontent['source']) && $fcontent['source'] == 'plugin'){
				$temp_options = Array('simple_table' => __('Basic HTML Table','wp-easy-events'),'adv_table'=>__('Advanced Datagrid Table','wp-easy-events'),'cust_table' => __('Default','wp-easy-events'));
			}
			else {
				$temp_options = Array('simple_table' => __('Basic HTML Table','wp-easy-events'),'adv_table'=>__('Advanced Datagrid Table','wp-easy-events'));
			}
			$tab_fields = Array(
					'ajax_search' => Array('label' => __('Enable Ajax','wp-easy-events'), 'type' => 'checkbox', 'upgrade' => true, 'disabled' => true),
					'enable_operators' => Array('label' => __('Enable Search Operators','wp-easy-events'), 'type' => 'checkbox', 'upgrade' => true, 'disabled' => true),
					'display_records' => Array('label' => __('Display Records On Page Load','wp-easy-events'), 'type' => 'checkbox', 'upgrade' => true, 'disabled' => true),
					'result_templ' => Array('label' => __('Result Template Type','wp-easy-events'), 'type' => 'select', 'options' => $temp_options, 'upgrade' => true, 'disabled' => true),
					'adv_search' => Array('label' => __('Search','wp-easy-events'), 'type' => 'checkbox', 'depend' => 'settings_result_templ_adv_table', 'upgrade' => true, 'disabled' => true),
					'adv_click' => Array('label' => __('Click to Select','wp-easy-events'), 'type' => 'checkbox', 'depend' => 'settings_result_templ_adv_table', 'upgrade' => true, 'disabled' => true),
					'adv_show_col' => Array('label' => __('Show Columns','wp-easy-events'), 'type' => 'checkbox', 'depend' => 'settings_result_templ_adv_table', 'upgrade' => true, 'disabled' => true),
					'adv_show_export' => Array('label' => __('Show Export','wp-easy-events'), 'type' => 'checkbox', 'depend' => 'settings_result_templ_adv_table', 'upgrade' => true, 'disabled' => true),
					'adv_show_toggle' => Array('label' => __('Show Toggle','wp-easy-events'), 'type' => 'checkbox', 'depend' => 'settings_result_templ_adv_table', 'upgrade' => true, 'disabled' => true),
					'adv_show_all' => Array('label' => __('Show All Records','wp-easy-events'), 'type' => 'checkbox', 'depend' => 'settings_result_templ_adv_table', 'upgrade' => true, 'disabled' => true),
					'adv_page_size' => Array('label' => __('Page Size','wp-easy-events'), 'type' => 'text', 'depend' => 'settings_adv_show_all_0','size'=> 'small', 'upgrade' => true, 'disabled' => true),
					'adv_page_list' => Array('label' => __('Page List','wp-easy-events'), 'type' => 'text', 'depend' => 'settings_adv_show_all_0','desc'=> __('Enter semicolon separated values such as 10, 20, 50, all','wp-easy-events'), 'upgrade' => true, 'disabled' => true),
					'adv_maintain' => Array('label' => __('Maintain Selected on Page Change','wp-easy-events'), 'type' => 'checkbox', 'depend' => 'settings_adv_show_all_0', 'upgrade' => true, 'disabled' => true),
					'result_fields' => Array('label' => __('Result Fields','wp-easy-events'), 'type' => 'select','options' => $result_options, 'multiple' => true, 'order' => true, 'upgrade' => true, 'disabled' => true),
					'noresult_msg' => Array('label' => __('No Result Message','wp-easy-events'), 'type' => 'textarea','default'=>__('Your search returned no results.','wp-easy-events'))
					);
			break;
		case 'code':
			$tab_fields = Array('css_enq' => Array('label' => __('CDN CSS File','wp-easy-events'), 'type' => 'textarea', 'desc' => __('Enter semicolon separated CSS file urls starting with http(s).','wp-easy-events')),
					'js_enq' => Array('label' => __('CDN JS File','wp-easy-events'), 'type' => 'textarea', 'desc' => __('Enter semicolon separated JavaScript file urls starting with http(s).','wp-easy-events'))
			);
			break;
	}
	echo '<div class="tab-content" id="tab-' . esc_attr($mytab) .'"';
	if ($mytab != $active_tab ){ 
		echo 'style="display:none;"'; 
	} 
	echo '>';
	echo '<div class="emd-form-builder-fields-settings">';
	echo emd_form_builder_lite_show_setting($tab_fields,$fcontent);
	echo '</div>';
	echo '</div>';
}
function emd_form_builder_lite_show_setting($tab_fields,$fcontent){
	if(!empty($tab_fields)){
		foreach($tab_fields as $skey => $sval){
			echo '<div class="emd-form-builder-field-setting"';
			if(!empty($sval['depend'])){
				echo ' data-depend="' . esc_attr($sval['depend']) . '"';
			}
			if(!empty($sval['depend2'])){
				echo ' data-depend2="' . esc_attr($sval['depend2']) . '"';
			}
			echo '>';
			if($sval['type'] != 'checkbox'){
				echo '<label for="emd-form-' . esc_attr($skey) . '" class="emd-label';
				if(!empty($sval['required'])){
					echo ' required';
				}
				echo '">' . esc_html($sval['label']);
				if(isset($sval['upgrade']) && $sval['upgrade'] === true){
					echo '<span class="dashicons dashicons-cart upgrade-pro" data-upgrade="' . esc_attr($sval['label']) . '"></span>';
				}
				echo '</label>';
			}
			if(empty($sval['size'])){
				$sval['size'] = 'medium';
			}
			switch($sval['type']){
				case 'text':
					echo '<input type="text" id="settings_' . esc_attr($skey) . '" class="input-' . esc_attr($sval['size']) . '" name="settings[' . esc_attr($skey) . ']"';
					if(!empty($fcontent['settings'][$skey])){
						echo ' value="' . esc_attr($fcontent['settings'][$skey]) . '"';
					}
					elseif(!empty($sval['default'])){
						echo ' value="' . esc_attr($sval['default']) . '"';
					}
					if(isset($sval['disabled']) && $sval['disabled'] === true){
						echo ' disabled';
					}
					echo '/>'; 
					break;
				case 'textarea':
					echo '<textarea id="settings_' . esc_attr($skey) . '" name="settings[' . esc_attr($skey) . ']" class="input-medium">';
					if(!empty($fcontent['settings'][$skey])){
 						echo esc_textarea($fcontent['settings'][$skey]);
					}
					elseif(!empty($sval['default'])){
						echo esc_textarea($sval['default']);
					}
					echo '</textarea>'; 
					break;
				case 'select':
					if(isset($sval['multiple']) && $sval['multiple'] === true){
						echo '<select id="settings_' . esc_attr($skey) . '" name="settings[' . esc_attr($skey) . '][]" multiple';
					}
					else {
						echo '<select id="settings_' . esc_attr($skey) . '" name="settings[' . esc_attr($skey) . ']"';
					}
						if(isset($sval['disabled']) && $sval['disabled'] === true){
							echo ' disabled';
						}
						echo '>';
						if(!empty($sval['order']) && !empty($fcontent['settings'][$skey])){
							foreach($fcontent['settings'][$skey] as $saved){
								echo '<option value="' . esc_attr($saved) . '" selected';
								echo '>' . esc_html($sval['options'][$saved]) . '</option>';
							}
							foreach($sval['options'] as $kopt => $vopt){
								if(!in_array($kopt,$fcontent['settings'][$skey])){
									echo '<option value="' . esc_attr($kopt) . '"';
									echo '>' . esc_html($vopt) . '</option>';
								}
							}
						}
						else {
							foreach($sval['options'] as $kopt => $vopt){
								echo '<option value="' . esc_attr($kopt) . '"';
								if(!empty($fcontent['settings'][$skey]) && is_array($fcontent['settings'][$skey]) && in_array($kopt,$fcontent['settings'][$skey])){
									echo ' selected';
								}
								elseif(!empty($fcontent['settings'][$skey]) && $fcontent['settings'][$skey] == $kopt){
									echo ' selected';
								}
								echo '>' . esc_html($vopt) . '</option>';
							}
						}
						echo  '</select>';
					break;
				case 'checkbox':
					echo '<input type="checkbox" id="settings_' . esc_attr($skey) . '" name="settings[' . esc_attr($skey) . ']"';
					if(!empty($fcontent['settings'][$skey])){
						echo ' checked';
					}
					if(isset($sval['disabled']) && $sval['disabled'] === true){
						echo ' disabled';
					}
					echo ' value=1>';
					break;
				case 'radio':
					foreach($sval['options'] as $kopt => $vopt){
						echo '<div class="emd-form-check radio">';
						echo '<input type="radio" id="settings_' . esc_attr($skey) . '" name="settings[' . esc_attr($skey) . ']"';
						if(!empty($fcontent['settings'][$skey]) && $fcontent['settings'][$skey] == $kopt){
							echo ' checked';
						}
						if(isset($sval['disabled']) && $sval['disabled'] === true){
							echo ' disabled';
						}
						echo ' value=' . esc_attr($kopt) . '>';
						echo '<label for="emd-form-' . esc_attr($skey) . '" class="emd-label inline';
						echo '">' . esc_html($vopt) . '</label>';
						echo '</div>';
					}
					break;
			}
			if($sval['type'] == 'checkbox'){
				echo '<label for="emd-form-' . esc_attr($skey) . '" class="emd-label inline';
				if(!empty($sval['required'])){
					echo ' required';
				}
				echo '">' . esc_html($sval['label']);
				if(isset($sval['upgrade']) && $sval['upgrade'] === true){
					echo '<span class="dashicons dashicons-cart upgrade-pro" data-upgrade="' . esc_attr($sval['label']) . '"></span>';
				}
 				echo '</label>';
			}
			if(!empty($sval['desc'])){
				echo '<p class="description">' . esc_html($sval['desc']) . '</p>';
			}
			echo '</div>';
		}
	}
}
function emd_form_builder_lite_save_settings($app){
	if(!empty($_POST['stype']) && $_POST['stype'] == 'edit' && !empty($_POST['id']) && !empty($_POST['settings'])){
		$myform = get_post((int) $_POST['id']);
		$fcontent = json_decode($myform->post_content,true);
		$new_settings = $fcontent['settings'];
		if(is_array($_POST['settings'])){
			foreach($_POST['settings'] as $skey => $sval){
				if(!in_array($skey,Array('result_fields','result_templ','ajax_search','enable_operators','display_records','form_class','css_enq','js_enq'))){
					$new_settings[$skey] = sanitize_text_field($sval);
				}
			}
		}
		$fcontent['settings'] = $new_settings;
		$form_data = array(
			'ID' => (int) $_POST['id'],
			'post_content' => wp_slash(json_encode($fcontent,true)),
		);
		$res = wp_update_post($form_data);
		if(!is_wp_error($res)){
			emd_form_builder_lite_settings('edit',$app,(int) $_GET['form_id'],'success');
		}
		else {
			emd_form_builder_lite_settings('edit',$app,(int) $_GET['form_id'],'error');
		}
	}
	else if(!empty($_POST['stype']) && $_POST['stype'] == 'new' && empty($_POST['id']) && !empty($_POST['settings'])){
		$fcontent = Array();
		$fcontent['type'] = sanitize_text_field($_POST['settings']['type']);
		$fcontent['app'] = sanitize_text_field($_POST['app']);
		$fcontent['name'] = strtolower(str_replace(" ","_",sanitize_text_field($_POST['settings']['title'])));

		$fcontent['entity'] = sanitize_text_field($_POST['settings']['entity']);
		$fcontent['settings'] = isset( $_POST['settings'] ) ? array_map('sanitize_text_field',$_POST['settings']) : array();
		$fcontent['source']  = 'user';
		$fcontent['layout']  = Array();
		$form_data = array(
			'post_title' => sanitize_text_field($_POST['settings']['title']),
			'post_type' => 'emd_form',
			'post_status' => 'publish',
			'post_content' => wp_slash(json_encode($fcontent,true)),
		);
		if ($id = wp_insert_post($form_data)) {
			emd_form_builder_lite_settings('edit',$app,$id,'success');
		}
		else {
			emd_form_builder_lite_settings('new',$app,0,'error');
		}
	}
	else {
		emd_form_builder_lite_settings('edit',$app,(int) $_GET['form_id'],'error');
	}
}
