<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;
?>
<div id="emd-register-container" style="display:none;">
<form class="emd-register-form emdloginreg-container" id="emd_register_form" method="post" action="<?php echo get_permalink($post->ID); ?>">
<fieldset>
<legend><?php esc_html_e( 'Register', 'wp-easy-events' ); ?></legend>
<div class="emd-form-row emd-row" style="display:flex;">
<div class="emd-form-field emd-col emd-md-12 emd-sm-12 emd-xs-12 emd-reg">
<div class="emd-form-group emd-login-username">
<label for="emd_user_login"><span><?php esc_html_e('Username', 'wp-easy-events' ); ?></span>
<span class="emd-fieldicons-wrap">
<a href="#" data-html="true" tabindex=-1 data-toggle="tooltip" title="<?php esc_html_e('Username field is required','wp-easy-events');?>" id="req_user_login" class="helptip">
<span class="field-icons required" aria-required="true"></span></a>
</span>
</label>
<input name="emd_user_login" id="emd_user_login" class="text required emd-input-md emd-form-control verify_reg" type="text"/>
</div>
</div>
</div>
<div class="emd-form-row emd-row" style="display:flex;">
<div class="emd-form-field emd-col emd-md-12 emd-sm-12 emd-xs-12 emd-reg">
<div class="emd-form-group emd-login-username">
<label for="emd-user-email"><span><?php esc_html_e('Email', 'wp-easy-events' ); ?></span>
<span class="emd-fieldicons-wrap">
<a href="#" data-html="true" tabindex=-1 data-toggle="tooltip" title="<?php esc_html_e('Email field is required','wp-easy-events');?>" id="req_user_login" class="helptip">
<span class="field-icons required" aria-required="true"></span></a>
</span>
</label>
<input name="emd_user_email" id="emd-user-email" class="text email required emd-input-md emd-form-control check_email" type="text"/>
</div>
</div>
</div>
<div class="emd-form-row emd-row" style="display:flex;">
<div class="emd-form-field emd-col emd-md-12 emd-sm-12 emd-xs-12 emd-reg">
<div class="emd-form-group emd-login-username">
<label for="emd-user-fname"><span><?php esc_html_e('First Name', 'wp-easy-events' ); ?></span>
</label>
<input name="emd_user_fname" id="emd-user-fname" class="text emd-input-md emd-form-control" type="text"/>
</div>
</div>
</div>
<div class="emd-form-row emd-row" style="display:flex;">
<div class="emd-form-field emd-col emd-md-12 emd-sm-12 emd-xs-12 emd-reg">
<div class="emd-form-group emd-login-username">
<label for="emd-user-lname"><span><?php esc_html_e('Last Name', 'wp-easy-events' ); ?></span>
</label>
<input name="emd_user_lname" id="emd-user-lname" class="text emd-input-md emd-form-control" type="text"/>
</div>
</div>
</div>
<div class="emd-form-row emd-row" style="display:flex;">
<div class="emd-form-field emd-col emd-md-12 emd-sm-12 emd-xs-12 emd-reg">
<div class="emd-form-group emd-login-username">
<label for="emd-user-pass"><span><?php esc_html_e('Password', 'wp-easy-events' ); ?></span>
<span class="emd-fieldicons-wrap">
<a href="#" data-html="true" tabindex=-1 data-toggle="tooltip" title="<?php esc_html_e('Password field is required','wp-easy-events');?>" id="req_user_login" class="helptip">
<span class="field-icons required" aria-required="true"></span></a>
</span>
</label>
<input name="emd_user_pass" id="emd-user-pass" class="password required emd-input-md emd-form-control check_passw" type="password"/>
</div>
</div>
</div>
<div class="emd-form-row emd-row" style="display:flex;">
<div class="emd-form-field emd-col emd-md-12 emd-sm-12 emd-xs-12 emd-reg">
<div class="emd-form-group emd-login-username">
<label for="emd-user-pass2"><span><?php esc_html_e('Confirm Password', 'wp-easy-events' ); ?></span>
<span class="emd-fieldicons-wrap">
<a href="#" data-html="true" tabindex=-1 data-toggle="tooltip" title="<?php esc_html_e('Confirm Password field is required','wp-easy-events');?>" id="req_user_login" class="helptip">
<span class="field-icons required" aria-required="true"></span></a>
</span>
</label>
<input name="emd_user_pass2" id="emd-user-pass2" class="password required emd-input-md emd-form-control check_passw2" type="password"/>
</div>
</div>
</div>
<div>
<input type="hidden" name="emd_redirect" value="<?php echo esc_url(get_permalink($post->ID)); ?>"/>
<input type="hidden" name="emd_register_nonce" value="<?php echo wp_create_nonce( 'emd-register-nonce' ); ?>"/>
<input type="hidden" name="emd_action" value="wp_easy_events_user_register"/>

<input type="submit" id="emd-register-submit" class="emd_submit button" name="emd_register_submit" value="<?php esc_html_e( 'Register', 'wp-easy-events' ); ?>"/>
</div>
<div style="clear:both">
<p class="emd-login-link" style="float:right">
<a href="">
<?php esc_html_e( 'Login', 'wp-easy-events' ); ?>
</a>
</p>
</div>
</fieldset><!--end #emd_register_fields-->
</form>
</div>
