<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'EMD_MB_Datetime_Field' ) )
{
	class EMD_MB_Datetime_Field extends EMD_MB_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			$url_css = EMD_MB_CSS_URL . 'jqueryui';
			wp_register_script( 'jquery-ui-timepicker', EMD_MB_JS_URL . 'jqueryui/jquery-ui-timepicker-addon.js', array( 'jquery-ui-datepicker', 'jquery-ui-slider' ), '0.9.7', true );
			wp_enqueue_style( 'jquery-ui-timepicker-css', "{$url_css}/jquery-ui-timepicker-addon.css");
			$deps = array( 'jquery-ui-datepicker', 'jquery-ui-timepicker' );

                        $locale = get_locale();
                        $date_vars['closeText'] = __('Done','wp-easy-events');
                        $date_vars['prevText'] = __('Prev','wp-easy-events');
                        $date_vars['nextText'] = __('Next','wp-easy-events');
                        $date_vars['currentText'] = __('Today','wp-easy-events');
                        $date_vars['monthNames'] = Array(__('January','wp-easy-events'),__('February','wp-easy-events'),__('March','wp-easy-events'),__('April','wp-easy-events'),__('May','wp-easy-events'),__('June','wp-easy-events'),__('July','wp-easy-events'),__('August','wp-easy-events'),__('September','wp-easy-events'),__('October','wp-easy-events'),__('November','wp-easy-events'),__('December','wp-easy-events'));
                        $date_vars['monthNamesShort'] = Array(__('Jan','wp-easy-events'),__('Feb','wp-easy-events'),__('Mar','wp-easy-events'),__('Apr','wp-easy-events'),__('May','wp-easy-events'),__('Jun','wp-easy-events'),__('Jul','wp-easy-events'),__('Aug','wp-easy-events'),__('Sep','wp-easy-events'),__('Oct','wp-easy-events'),__('Nov','wp-easy-events'),__('Dec','wp-easy-events'));
                        $date_vars['dayNames'] = Array(__('Sunday','wp-easy-events'),__('Monday','wp-easy-events'),__('Tuesday','wp-easy-events'),__('Wednesday','wp-easy-events'),__('Thursday','wp-easy-events'),__('Friday','wp-easy-events'),__('Saturday','wp-easy-events'));
                        $date_vars['dayNamesShort'] = Array(__('Sun','wp-easy-events'),__('Mon','wp-easy-events'),__('Tue','wp-easy-events'),__('Wed','wp-easy-events'),__('Thu','wp-easy-events'),__('Fri','wp-easy-events'),__('Sat','wp-easy-events'));
                        $date_vars['dayNamesMin'] = Array(__('Su','wp-easy-events'),__('Mo','wp-easy-events'),__('Tu','wp-easy-events'),__('We','wp-easy-events'),__('Th','wp-easy-events'),__('Fr','wp-easy-events'),__('Sa','wp-easy-events'));
                        $date_vars['weekHeader'] = __('Wk','wp-easy-events');

			$time_vars['timeOnlyTitle'] = __('Choose Time','wp-easy-events');
			$time_vars['timeText'] = __('Time','wp-easy-events');
			$time_vars['hourText'] = __('Hour','wp-easy-events');
			$time_vars['minuteText'] = __('Minute','wp-easy-events');
			$time_vars['secondText'] = __('Second','wp-easy-events');
			$time_vars['millisecText'] = __('Millisecond','wp-easy-events');
			$time_vars['timezoneText'] = __('Time Zone','wp-easy-events');
			$time_vars['currentText'] = __('Now','wp-easy-events');
			$time_vars['closeText'] = __('Done','wp-easy-events');

                        $vars['date'] = $date_vars;
                        $vars['time'] = $time_vars;
                        $vars['locale'] = $locale;

			wp_enqueue_script( 'emd-mb-datetime', EMD_MB_JS_URL . 'datetime.js', $deps, EMD_MB_VER, true );
                        wp_localize_script( 'emd-mb-datetime', 'dtvars', $vars);
		}

		/**
		 * Get field HTML
		 *
		 * @param mixed  $meta
		 * @param array  $field
		 *
		 * @return string
		 */
		static function html( $meta, $field )
		{
			if($meta != '')
                        {
                                if($field['js_options']['timeFormat'] == 'hh:mm')
                                {
                                        $getformat = 'Y-m-d H:i';
                                }
                                else
                                {
                                        $getformat = 'Y-m-d H:i:s';
                                }
				if(DateTime::createFromFormat($getformat,$meta)){
                                	$meta = DateTime::createFromFormat($getformat,$meta)->format(self::translate_format($field));
				}
                        }
                        return sprintf(
                                '<input type="text" class="emd-mb-datetime" name="%s" value="%s" id="%s" size="%s" data-options="%s" readonly/>',
                                $field['field_name'],
                                $meta,
                                isset( $field['clone'] ) && $field['clone'] ? '' : $field['id'],
                                $field['size'],
                                esc_attr( wp_json_encode( $field['js_options'] ) )
                        );
		}

		/**
		 * Calculates the timestamp from the datetime string and returns it
		 * if $field['timestamp'] is set or the datetime string if not
		 *
		 * @param mixed $new
		 * @param mixed $old
		 * @param int   $post_id
		 * @param array $field
		 *
		 * @return string|int
		 */
		/*static function value( $new, $old, $post_id, $field )
		{
			if ( !$field['timestamp'] )
				return $new;

			$d = DateTime::createFromFormat( self::translate_format( $field ), $new );
			return $d ? $d->getTimestamp() : 0;
		}*/

		/**
		 * Normalize parameters for field
		 *
		 * @param array $field
		 *
		 * @return array
		 */
		static function normalize_field( $field )
		{
			$field = wp_parse_args( $field, array(
				'size'       => 30,
				'js_options' => array(),
				'timestamp'  => false,
			) );

			// Deprecate 'format', but keep it for backward compatible
			// Use 'js_options' instead
			$field['js_options'] = wp_parse_args( $field['js_options'], array(
				'dateFormat'      => empty( $field['format'] ) ? 'yy-mm-dd' : $field['format'],
				'timeFormat'      => 'hh:mm:ss',
				'showButtonPanel' => true,
				'separator'       => ' ',
				'changeMonth' => true,
				'changeYear' => true,
				'yearRange' => '-100:+10',
			) );

			return $field;
		}

		/**
		 * Returns a date() compatible format string from the JavaScript format
		 *
		 * @see http://www.php.net/manual/en/function.date.php
		 *
		 * @param array $field
		 *
		 * @return string
		 */
		static function translate_format( $field )
		{
			return strtr( $field['js_options']['dateFormat'], self::$date_format_translation )
				. $field['js_options']['separator']
				. strtr( $field['js_options']['timeFormat'], self::$time_format_translation );
		}
		static function save( $new, $old, $post_id, $field )
                {
                        $name = $field['id'];
                        if ( '' === $new)
                        {
                                delete_post_meta( $post_id, $name );
                                return;
                        }
                        if($field['js_options']['timeFormat'] == 'hh:mm')
                        {
                                $getformat = 'Y-m-d H:i';
                        }
                        else
                        {
                                $getformat = 'Y-m-d H:i:s';
                        }
			if(DateTime::createFromFormat(self::translate_format($field), $new)){
                        	$new = DateTime::createFromFormat(self::translate_format($field), $new)->format($getformat);
                        	update_post_meta( $post_id, $name, $new );
			}
                }
	}
}
