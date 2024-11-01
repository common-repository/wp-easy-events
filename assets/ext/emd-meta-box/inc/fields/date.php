<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'EMD_MB_Date_Field' ) )
{
	class EMD_MB_Date_Field extends EMD_MB_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			$deps = array( 'jquery-ui-datepicker' );
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
		
			$vars['date'] = $date_vars;
			$vars['locale'] = $locale;	
			wp_enqueue_script( 'emd-mb-date', EMD_MB_JS_URL . 'date.js', $deps, EMD_MB_VER, true );
			wp_localize_script( 'emd-mb-date', 'vars', $vars);
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
				if(DateTime::createFromFormat('Y-m-d',$meta)){
                                	$meta = DateTime::createFromFormat('Y-m-d',$meta)->format(self::translate_format($field));
				}
                        }
			return sprintf(
				'<input type="text" class="emd-mb-date" name="%s" value="%s" id="%s" size="%s" data-options="%s" %s readonly/>',
				$field['field_name'],
				$meta,
				isset( $field['clone'] ) && $field['clone'] ? '' : $field['id'],
				$field['size'],
				esc_attr( wp_json_encode( $field['js_options'] ) ),
				isset($field['data-cell']) ? "data-cell='{$field['data-cell']}'" : ''
			);
		}

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
			) );

			// Deprecate 'format', but keep it for backward compatible
			// Use 'js_options' instead
			$field['js_options'] = wp_parse_args( $field['js_options'], array(
				'dateFormat'      => empty( $field['format'] ) ? 'yy-mm-dd' : $field['format'],
				'showButtonPanel' => true,
				'changeMonth' => true,
				'changeYear' => true,
				'yearRange' => '-100:+10'
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
                        return strtr( $field['js_options']['dateFormat'], self::$date_format_translation );
                }

                static function save( $new, $old, $post_id, $field )
                {
                        $name = $field['id'];
                        if ( '' === $new)
                        {
                                delete_post_meta( $post_id, $name );
                                return;
                        }
			if(DateTime::createFromFormat(self::translate_format($field), $new)){
                        	$new = DateTime::createFromFormat(self::translate_format($field), $new)->format('Y-m-d');
                        	update_post_meta( $post_id, $name, $new );
			}
                }
	}
}
