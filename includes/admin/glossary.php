<?php
/**
 * Settings Glossary Functions
 *
 * @package WP_EASY_EVENTS
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_action('wp_easy_events_settings_glossary', 'wp_easy_events_settings_glossary');
/**
 * Display glossary information
 * @since WPAS 4.0
 *
 * @return html
 */
function wp_easy_events_settings_glossary() {
	global $title;
?>
<div class="wrap">
<h2><?php echo esc_html($title); ?></h2>
<p><?php esc_html_e('WP Easy Events is an easy to use event management, rsvp and ticketing plugin.', 'wp-easy-events'); ?></p>
<p><?php esc_html_e('The below are the definitions of entities, attributes, and terms included in WP Easy Events.', 'wp-easy-events'); ?></p>
<div id="glossary" class="accordion-container">
<ul class="outer-border">
<li id="emd_event_attendee" class="control-section accordion-section">
<h3 class="accordion-section-title hndle" tabindex="4"><?php esc_html_e('Attendees', 'wp-easy-events'); ?></h3>
<div class="accordion-section-content">
<div class="inside">
<table class="form-table"><p class"lead"><?php esc_html_e('A person or organization attending the event.', 'wp-easy-events'); ?></p><tr><th style='font-size: 1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php esc_html_e('Attributes', 'wp-easy-events'); ?></div></th></tr>
<tr>
<th><?php esc_html_e('Ticket ID', 'wp-easy-events'); ?></th>
<td><?php esc_html_e('Unique identifier for every ticket Being a unique identifier, it uniquely distinguishes each instance of Attendee entity. Ticket ID is filterable in the admin area. Ticket ID does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('First Name', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' First Name is filterable in the admin area. First Name does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Last Name', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Last Name is filterable in the admin area. Last Name does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Email', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Email is filterable in the admin area. Email does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Full Name', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Full Name does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Quantity', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Quantity is a required field. Quantity is filterable in the admin area. Quantity has a default value of <b>1</b>.', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Check-in', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Check-in is filterable in the admin area. Check-in does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Form Name', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Form Name is filterable in the admin area. Form Name has a default value of <b>admin</b>.', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Form Submitted By', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Form Submitted By is filterable in the admin area. Form Submitted By does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Form Submitted IP', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Form Submitted IP is filterable in the admin area. Form Submitted IP does not have a default value. ', 'wp-easy-events'); ?></td>
</tr><tr><th style='font-size: 1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php esc_html_e('Relationships', 'wp-easy-events'); ?></div></th></tr>
<tr>
<th><?php esc_html_e('Events', 'wp-easy-events'); ?></th>
<td><?php esc_html_e('Allows to display and create connections with Events', 'wp-easy-events'); ?>. <?php esc_html_e('One instance of Attendees can associated with <b>only one</b> instance of Events', 'wp-easy-events'); ?>.  <?php esc_html_e('The relationship can be set up in the edit area of Attendees using Events relationship box. ', 'wp-easy-events'); ?> </td>
</tr></table>
</div>
</div>
</li><li id="emd_wpe_event" class="control-section accordion-section">
<h3 class="accordion-section-title hndle" tabindex="1"><?php esc_html_e('Events', 'wp-easy-events'); ?></h3>
<div class="accordion-section-content">
<div class="inside">
<table class="form-table"><p class"lead"><?php esc_html_e('Any planned public or social occasion.', 'wp-easy-events'); ?></p><tr><th style='font-size: 1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php esc_html_e('Attributes', 'wp-easy-events'); ?></div></th></tr>
<tr>
<th><?php esc_html_e('Title', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Title is a required field. Being a unique identifier, it uniquely distinguishes each instance of Event entity. Title does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Description', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Description does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Excerpt', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Excerpt does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Featured', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Featured is filterable in the admin area. Featured does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Registration Type', 'wp-easy-events'); ?></th>
<td><?php esc_html_e('<a href="https://emdplugins.com/plugins/wp-easy-event-woocommerce-extension/?pk_campaign=wpee-pro&amp;pk_kwd=regtypelink" title="Buy WP Easy Event WooCommerce Extension Now">WooCommerce</a> and <a href="https://emdplugins.com/plugins/wp-easy-event-easy-digital-downloads-extension/?pk_campaign=wpee-pro&amp;pk_kwd=regtypelink" title="Buy WP Easy Event Easy Digital Downloads Extension Now">Easy Digital Downloads</a> types require corresponding extensions to work. Registration Type is filterable in the admin area. Registration Type has a default value of <b>\'none\'</b>.', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Start Date', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Start Date does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('End Date', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' End Date does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Website', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Website does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Display Timezone On Event Page', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Display Timezone On Event Page does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Timezone', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Timezone is filterable in the admin area. Timezone has a default value of <b>\'UTC0\'</b>.', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Cost', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Cost does not have a default value. ', 'wp-easy-events'); ?></td>
</tr><tr><th style='font-size:1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php esc_html_e('Taxonomies', 'wp-easy-events'); ?></div></th></tr>
<tr>
<th><?php esc_html_e('Category', 'wp-easy-events'); ?></th>

<td><?php esc_html_e(' Category accepts multiple values like tags', 'wp-easy-events'); ?>. <?php esc_html_e('Category does not have a default value', 'wp-easy-events'); ?>.<div class="taxdef-block"><p><?php esc_html_e('There are no preset values for <b>Category.</b>', 'wp-easy-events'); ?></p></div></td>
</tr>

<tr>
<th><?php esc_html_e('Tag', 'wp-easy-events'); ?></th>

<td><?php esc_html_e(' Tag accepts multiple values like tags', 'wp-easy-events'); ?>. <?php esc_html_e('Tag does not have a default value', 'wp-easy-events'); ?>.<div class="taxdef-block"><p><?php esc_html_e('There are no preset values for <b>Tag.</b>', 'wp-easy-events'); ?></p></div></td>
</tr>
<tr><th style='font-size: 1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php esc_html_e('Relationships', 'wp-easy-events'); ?></div></th></tr>
<tr>
<th><?php esc_html_e('Venues', 'wp-easy-events'); ?></th>
<td><?php esc_html_e('Allows to display and create connections with Venues', 'wp-easy-events'); ?>. <?php esc_html_e('One instance of Events can associated with <b>only one</b> instance of Venues', 'wp-easy-events'); ?>.  <?php esc_html_e('The relationship can be set up in the edit area of Venues using Events or in the edit area of Events using Venues relationship boxes', 'wp-easy-events'); ?>. <?php esc_html_e('This relationship is required when publishing new Events', 'wp-easy-events'); ?>. </td>
</tr>
<tr>
<th><?php esc_html_e('Attendees', 'wp-easy-events'); ?></th>
<td><?php esc_html_e('Allows to display and create connections with Attendees', 'wp-easy-events'); ?>. <?php esc_html_e('One instance of Events can associated with many instances of Attendees', 'wp-easy-events'); ?>.  <?php esc_html_e('The relationship can be set up in the edit area of Attendees using Events relationship box', 'wp-easy-events'); ?>. </td>
</tr>
<tr>
<th><?php esc_html_e('Organizers', 'wp-easy-events'); ?></th>
<td><?php esc_html_e('Allows to display and create connections with Organizers', 'wp-easy-events'); ?>. <?php esc_html_e('One instance of Events can associated with many instances of Organizers, and vice versa', 'wp-easy-events'); ?>.  <?php esc_html_e('The relationship can be set up in the edit area of Events using Organizers or in the edit area of Organizers using Events relationship boxes', 'wp-easy-events'); ?>. </td>
</tr></table>
</div>
</div>
</li><li id="emd_event_organizer" class="control-section accordion-section">
<h3 class="accordion-section-title hndle" tabindex="2"><?php esc_html_e('Organizers', 'wp-easy-events'); ?></h3>
<div class="accordion-section-content">
<div class="inside">
<table class="form-table"><p class"lead"><?php esc_html_e('Person or company that organizes events', 'wp-easy-events'); ?></p><tr><th style='font-size: 1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php esc_html_e('Attributes', 'wp-easy-events'); ?></div></th></tr>
<tr>
<th><?php esc_html_e('Title', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Title is a required field. Being a unique identifier, it uniquely distinguishes each instance of Organizer entity. Title does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Detail', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Detail does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Email', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Email is filterable in the admin area. Email does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Phone', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Phone is filterable in the admin area. Phone does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Website', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Website is filterable in the admin area. Website does not have a default value. ', 'wp-easy-events'); ?></td>
</tr><tr><th style='font-size: 1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php esc_html_e('Relationships', 'wp-easy-events'); ?></div></th></tr>
<tr>
<th><?php esc_html_e('Events', 'wp-easy-events'); ?></th>
<td><?php esc_html_e('Allows to display and create connections with Events', 'wp-easy-events'); ?>. <?php esc_html_e('One instance of Organizers can associated with many instances of Events, and vice versa', 'wp-easy-events'); ?>.  <?php esc_html_e('The relationship can be set up in the edit area of Events using Organizers or in the edit area of Organizers using Events relationship boxes', 'wp-easy-events'); ?>. </td>
</tr></table>
</div>
</div>
</li><li id="emd_event_venues" class="control-section accordion-section">
<h3 class="accordion-section-title hndle" tabindex="3"><?php esc_html_e('Venues', 'wp-easy-events'); ?></h3>
<div class="accordion-section-content">
<div class="inside">
<table class="form-table"><p class"lead"><?php esc_html_e('', 'wp-easy-events'); ?></p><tr><th style='font-size: 1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php esc_html_e('Attributes', 'wp-easy-events'); ?></div></th></tr>
<tr>
<th><?php esc_html_e('Title', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Title is a required field. Being a unique identifier, it uniquely distinguishes each instance of Venue entity. Title does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Details', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Details does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Excerpt', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Excerpt does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Address', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Address is a required field. Address does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('City', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' City is a required field. City is filterable in the admin area. City does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('State', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' State is filterable in the admin area. State does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Postal Code', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Postal Code is filterable in the admin area. Postal Code does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Country', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Country is filterable in the admin area. Country does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Full Address', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Full Address does not have a default value. ', 'wp-easy-events'); ?></td>
</tr>
<tr>
<th><?php esc_html_e('Map', 'wp-easy-events'); ?></th>
<td><?php esc_html_e(' Map does not have a default value. ', 'wp-easy-events'); ?></td>
</tr><tr><th style='font-size: 1.1em;color:cadetblue;border-bottom: 1px dashed;padding-bottom: 10px;' colspan=2><div><?php esc_html_e('Relationships', 'wp-easy-events'); ?></div></th></tr>
<tr>
<th><?php esc_html_e('Events', 'wp-easy-events'); ?></th>
<td><?php esc_html_e('Allows to display and create connections with Events', 'wp-easy-events'); ?>. <?php esc_html_e('One instance of Venues can associated with many instances of Events', 'wp-easy-events'); ?>.  <?php esc_html_e('The relationship can be set up in the edit area of Venues using Events or in the edit area of Events using Venues relationship boxes', 'wp-easy-events'); ?>. <?php esc_html_e('This relationship is required when publishing new Venues', 'wp-easy-events'); ?>. </td>
</tr></table>
</div>
</div>
</li>
</ul>
</div>
</div>
<?php
}