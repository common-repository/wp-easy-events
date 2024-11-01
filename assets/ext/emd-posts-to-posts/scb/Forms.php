<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

// Data-aware form generator

class EmdScbForms {

	const TOKEN = '%input%';

	static function input_with_value( $args, $value ) {
		$field = EmdScbFormField::create( $args );

		return $field->render( $value );
	}

	static function input( $args, $formdata = null ) {
		$field = EmdScbFormField::create( $args );

		return $field->render( EmdScbForms::get_value( $args['name'], $formdata ) );
	}

	// Generates a table wrapped in a form
	static function form_table( $rows, $formdata = null ) {
		$output = '';
		foreach ( $rows as $row )
			$output .= self::table_row( $row, $formdata );

		$output = self::form_table_wrap( $output );

		return $output;
	}

	// Generates a form
	static function form( $inputs, $formdata = null, $nonce ) {
		$output = '';
		foreach ( $inputs as $input )
			$output .= self::input( $input, $formdata );

		$output = self::form_wrap( $output, $nonce );

		return $output;
	}

	// Generates a table
	static function table( $rows, $formdata = null ) {
		$output = '';
		foreach ( $rows as $row )
			$output .= self::table_row( $row, $formdata );

		$output = self::table_wrap( $output );

		return $output;
	}

	// Generates a table row
	static function table_row( $args, $formdata = null ) {
		return self::row_wrap( $args['title'], self::input( $args, $formdata ) );
	}


// ____________WRAPPERS____________


	static function form_table_wrap( $content, $nonce = 'update_options' ) {
		return self::form_wrap( self::table_wrap( $content ), $nonce );
	}

	static function form_wrap( $content, $nonce = 'update_options' ) {
		return emd_p2p_html( "form method='post' action=''",
			$content,
			wp_nonce_field( $nonce, '_wpnonce', $referer = true, $echo = false )
		);
	}

	static function table_wrap( $content ) {
		return emd_p2p_html( "table class='form-table'", $content );
	}

	static function row_wrap( $title, $content ) {
		return emd_p2p_html( "tr",
			emd_p2p_html( "th scope='row'", $title ),
			emd_p2p_html( "td", $content )
		);
	}


// ____________PRIVATE METHODS____________


// Utilities


	/**
	 * Generates the proper string for a name attribute.
	 *
	 * @param array|string $name The raw name
	 *
	 * @return string
	 */
	static function get_name( $name ) {
		$name = (array) $name;

		$name_str = array_shift( $name );

		foreach ( $name as $key ) {
			$name_str .= '[' . esc_attr( $key ) . ']';
		}

		return $name_str;
	}

	/**
	 * Traverses the formdata and retrieves the correct value.
	 *
	 * @param array|string $name The name of the value
	 * @param array $value The data that will be traversed
	 * @param mixed $fallback The value returned when the key is not found
	 *
	 * @return mixed
	 */
	static function get_value( $name, $value, $fallback = null ) {
		foreach ( (array) $name as $key ) {
			if ( !isset( $value[ $key ] ) )
				return $fallback;

			$value = $value[$key];
		}

		return $value;
	}

	/**
	 * Given a list of fields, validate some data.
	 *
	 * @param array $fields List of args that would be sent to EmdScbForms::input()
	 * @param array $data The data to validate. Defaults to $_POST
	 * @param array $to_update Existing data to populate. Necessary for nested values
	 *
	 * @return array
	 */
	static function validate_post_data( $fields, $data = null, $to_update = array() ) {
		if ( null === $data ) {
			$data = is_array( $_POST ) ? array_map( 'sanitize_text_field', $_POST ) : sanitize_text_field( $_POST ); 
			$data = stripslashes_deep( $data );
		}

		foreach ( $fields as $field ) {
			$value = EmdScbForms::get_value( $field['name'], $data );

			$fieldObj = EmdScbFormField::create( $field );

			$value = $fieldObj->validate( $value );

			if ( null !== $value )
				self::set_value( $to_update, $field['name'], $value );
		}

		return $to_update;
	}

	/**
	 * For multiple-choice fields, we can never distinguish between "never been set" and "set to none".
	 * For single-choice fields, we can't distinguish either, because of how self::update_meta() works.
	 * Therefore, the 'default' parameter is always ignored.
	 *
	 * @param array $args Field arguments.
	 * @param int $object_id The object ID the metadata is attached to
	 * @param string $meta_type
	 *
	 * @return string
	 */
	static function input_from_meta( $args, $object_id, $meta_type = 'post' ) {
		$single = ( 'checkbox' != $args['type'] && 'multiselect' != $args['type']);

		$key = (array) $args['name'];
		$key = end( $key );

		$value = get_metadata( $meta_type, $object_id, $key, $single );

		return self::input_with_value( $args, $value );
	}

	static function update_meta( $fields, $data, $object_id, $meta_type = 'post' ) {
		foreach ( $fields as $field_args ) {
			$key = $field_args['name'];

			if ( 'checkbox' == $field_args['type'] || 'multiselect' == $field_args['type'] ) {
				$new_values = isset( $data[$key] ) ? $data[$key] : array();

				$old_values = get_metadata( $meta_type, $object_id, $key );

				foreach ( array_diff( $new_values, $old_values ) as $value )
					add_metadata( $meta_type, $object_id, $key, $value );

				foreach ( array_diff( $old_values, $new_values ) as $value )
					delete_metadata( $meta_type, $object_id, $key, $value );
			} else {
				$value = $data[$key];

				if ( '' === $value ){
					delete_metadata( $meta_type, $object_id, $key );
				} else {
					if ('date' == $field_args['type'] ) {
						$value = date('Y-m-d',strtotime($value));
					} elseif ('datetime' == $field_args['type'] ) {
						$value = date('Y-m-d H:i:s',strtotime($value));
					} elseif('time' == $field_args['type'] ) {
						$value = date('H:i:s',strtotime($value));
					}
					update_metadata( $meta_type, $object_id, $key, $value );
				}
			}
		}
	}

	private static function set_value( &$arr, $name, $value ) {
		$name = (array) $name;

		$final_key = array_pop( $name );

		while ( !empty( $name ) ) {
			$key = array_shift( $name );

			if ( !isset( $arr[ $key ] ) )
				$arr[ $key ] = array();

			$arr =& $arr[ $key ];
		}

		$arr[ $final_key ] = $value;
	}
}


/**
 * A wrapper for EmdScbForms, containing the formdata
 */
class EmdScbForm {
	protected $data = array();
	protected $prefix = array();

	function __construct( $data, $prefix = false ) {
		if ( is_array( $data ) )
			$this->data = $data;

		if ( $prefix )
			$this->prefix = (array) $prefix;
	}

	function traverse_to( $path ) {
		$data = EmdScbForms::get_value( $path, $this->data );

		$prefix = array_merge( $this->prefix, (array) $path );

		return new EmdScbForm( $data, $prefix );
	}

	function input( $args ) {
		$value = EmdScbForms::get_value( $args['name'], $this->data );

		if ( !empty( $this->prefix ) ) {
			$args['name'] = array_merge( $this->prefix, (array) $args['name'] );
		}

		return EmdScbForms::input_with_value( $args, $value );
	}
}


interface EmdScbFormField_I {

	/**
	 * Generate the corresponding HTML for a field
	 *
	 * @param mixed $value The value to use
	 *
	 * @return string
	 */
	function render( $value = null );

	/**
	 * Validates a value against a field.
	 *
	 * @param mixed $value The value to check
	 *
	 * @return mixed null if the validation failed, sanitized value otherwise.
	 */
	function validate( $value );
}


abstract class EmdScbFormField implements EmdScbFormField_I {

	protected $args;

	public static function create( $args ) {
		if ( is_a( $args, 'EmdScbFormField_I' ) )
			return $args;

		if ( empty( $args['name'] ) ) {
			return trigger_error( 'Empty name', E_USER_WARNING );
		}

		if ( isset( $args['value'] ) && is_array( $args['value'] ) ) {
			$args['choices'] = $args['value'];
			unset( $args['value'] );
		}

		if ( isset( $args['values'] ) ) {
			$args['choices'] = $args['values'];
			unset( $args['values'] );
		}

		if ( isset( $args['extra'] ) && !is_array( $args['extra'] ) )
			$args['extra'] = shortcode_parse_atts( $args['extra'] );

		$args = wp_parse_args( $args, array(
			'desc' => '',
			'desc_pos' => 'after',
			'wrap' => EmdScbForms::TOKEN,
			'wrap_each' => EmdScbForms::TOKEN,
		) );

		// depends on $args['desc']
		if ( isset( $args['choices'] ) )
			self::_expand_choices( $args );

		switch ( $args['type'] ) {
		case 'radio':
			return new EmdScbRadiosField( $args );
		case 'select':
			return new EmdScbSelectField( $args );
		case 'multiselect':
			return new EmdScbMultiSelectField( $args );
		case 'checkbox':
			if ( isset( $args['choices'] ) )
				return new EmdScbMultipleChoiceField( $args );
			else
				return new EmdScbSingleCheckboxField( $args );
		case 'custom':
			return new EmdScbCustomField( $args );
		default:
			return new EmdScbTextField( $args );
		}
	}

	protected function __construct( $args ) {
		$this->args = $args;
	}

	public function __get( $key ) {
		return $this->args[ $key ];
	}

	public function __isset( $key ) {
		return isset( $this->args[ $key ] );
	}

	public function render( $value = null ) {
		if ( null === $value && isset( $this->default ) )
			$value = $this->default;

		$args = $this->args;

		if ( null !== $value )
			$this->_set_value( $args, $value );

		$args['name'] = EmdScbForms::get_name( $args['name'] );

		return str_replace( EmdScbForms::TOKEN, $this->_render( $args ), $this->wrap );
	}

	// Mutate the field arguments so that the value passed is rendered.
	abstract protected function _set_value( &$args, $value );

	// The actual rendering
	abstract protected function _render( $args );

	// Handle args for a single checkbox or radio input
	protected static function _checkbox( $args ) {
		$args = wp_parse_args( $args, array(
			'value' => true,
			'desc' => null,
			'checked' => false,
			'extra' => array(),
		) );

		foreach ( $args as $key => &$val )
			$$key = &$val;
		unset( $val );

		$extra['checked'] = $checked;

		if ( is_null( $desc ) && !is_bool( $value ) )
			$desc = str_replace( '[]', '', $value );

		return self::_input_gen( $args );
	}

	// Generate html with the final args
	protected static function _input_gen( $args ) {
		extract( wp_parse_args( $args, array(
			'value' => null,
			'desc' => null,
			'extra' => array()
		) ) );

		$extra['name'] = $name;

		if ( 'textarea' == $type ) {
			$input = emd_p2p_html( 'textarea', $extra, esc_textarea( $value ) );
		} else {
			$extra['value'] = $value;
			$extra['type'] = $type;
			$input = emd_p2p_html( 'input', $extra );
		}

		return self::add_label( $input, $desc, $desc_pos );
	}

	protected static function add_label( $input, $desc, $desc_pos ) {
		return emd_p2p_html( 'label', self::add_desc( $input, $desc, $desc_pos ) ) . "\n";
	}

	protected static function add_desc( $input, $desc, $desc_pos ) {
		if ( empty( $desc ) )
			return $input;

		if ( 'before' == $desc_pos )
			return $desc . ' ' . $input;
		else
			return $input . ' ' . $desc;
	}

	private static function _expand_choices( &$args ) {
		$choices =& $args['choices'];

		if ( !empty( $choices ) && !self::is_associative( $choices ) ) {
			if ( is_array( $args['desc'] ) ) {
				$choices = array_combine( $choices, $args['desc'] );	// back-compat
				$args['desc'] = false;
			} elseif ( !isset( $args['numeric'] ) || !$args['numeric'] ) {
				$choices = array_combine( $choices, $choices );
			}
		}
	}

	private static function is_associative( $array ) {
		$keys = array_keys( $array );
		return array_keys( $keys ) !== $keys;
	}
}


class EmdScbTextField extends EmdScbFormField {

	public function validate( $value ) {
		$sanitize = isset( $this->sanitize ) ? $this->sanitize : 'wp_filter_kses';

		return call_user_func( $sanitize, $value, $this );
	}

	protected function _render( $args ) {
		$date_class = '';
		if(in_array($args['type'],Array('date','datetime','time'))){
			$date_time_format_translation = array(
				'a' => 'tt', 'A' => 'TT', 'g' => 'h', 'h' => 'hh',
				'G' => 'H', 'H' => 'HH', 'i' => 'mm', 's' => 'ss', 'T' => 'z',
				'd' => 'dd', 'j' => 'd', 'l' => 'DD', 'D' => 'D', 'm' => 'mm',
				'n' => 'm', 'F' => 'MM', 'M' => 'M', 'Y' => 'yy' , 'y' => 'y',
			);
			switch($args['type']){
				case 'date':
					$dformat = get_option('date_format');
					$dformat = strtr($dformat,$date_time_format_translation);
					$data_options = '{"dateFormat": "' . $dformat . '"}';
					$date_class = 'emd-mb-date';
					break;
				case 'datetime':
					$tformat = get_option('time_format');
					$dformat = get_option('date_format');
					$tformat = strtr($tformat,$date_time_format_translation);
					$dformat = strtr($dformat,$date_time_format_translation);
					$data_options = '{"dateFormat": "' . $dformat . '","timeFormat": "' . $tformat . '","controlType": "select","oneLine": true}';
					$date_class = 'emd-mb-datetime';
					break;
				case 'time':
					$tformat = get_option('time_format');
					$tformat = strtr($tformat,$date_time_format_translation);
					$data_options = '{"timeFormat": "' . $tformat . '","controlType": "select","oneLine": true}';
					$date_class = 'emd-mb-time';
					break;
			}
			$args['type'] = 'text';
		}
		else {
			$date_class = 'regular-text';
			$data_options = '';
		}
		$extra['class'] = $date_class;
		if(!empty($data_options)){
			$extra['data-options'] = $data_options;
		}
		$args = wp_parse_args( $args, array(
			'value' => '',
			'desc_pos' => 'after',
			'extra' => $extra,
		) );

		foreach ( $args as $key => &$val )
			$$key = &$val;
		unset( $val );

		if ( !isset( $extra['id'] ) && !is_array( $name ) && false === strpos( $name, '[' ) )
			$extra['id'] = $name;

		return EmdScbFormField::_input_gen( $args );
	}

	protected function _set_value( &$args, $value ) {
		if($args['type'] == 'date'){
			$value = date(get_option('date_format'),strtotime($value));
		}
		if($args['type'] == 'datetime'){
			$value = date(get_option('date_format') . ' ' . get_option('time_format'),strtotime($value));
		}
		if($args['type'] == 'time'){
			$value = date(get_option('time_format'),strtotime($value));
		}
		$args['value'] = $value;
	}
}


abstract class EmdScbSingleChoiceField extends EmdScbFormField {

	public function validate( $value ) {
		if ( isset( $this->choices[ $value ] ) )
			return $value;

		return null;
	}

	protected function _render( $args ) {
		$args = wp_parse_args( $args, array(
			'numeric' => false,		// use numeric array instead of associative
			'selected' => array( 'foo' ),	// hack to make default blank
		) );

		return $this->_render_specific( $args );
	}

	protected function _set_value( &$args, $value ) {
		$args['selected'] = $value;
	}

	abstract protected function _render_specific( $args );
}


class EmdScbSelectField extends EmdScbSingleChoiceField {

	protected function _render_specific( $args ) {
		extract( wp_parse_args( $args, array(
			'text' => false,
			'extra' => array()
		) ) );

		$options = array();

		if ( false !== $text ) {
			$options[] = array(
				'value' => '',
				'selected' => ( $selected == array( 'foo' ) ),
				'title' => $text
			);
		}

		foreach ( $choices as $value => $title ) {
			$options[] = array(
				'value' => $value,
				'selected' => ( $value == $selected ),
				'title' => $title
			);
		}

		$opts = '';
		foreach ( $options as $option ) {
			extract( $option );

			$opts .= emd_p2p_html( 'option', compact( 'value', 'selected' ), $title );
		}

		$extra['name'] = $name;

		$input = emd_p2p_html( 'select', $extra, $opts );

		return EmdScbFormField::add_label( $input, $desc, $desc_pos );
	}
}

class EmdScbMultiSelectField extends EmdScbFormField {
	public function validate( $value ) {
		return array_intersect( array_keys( $this->choices ), (array) $value );
	}
	protected function _render( $args ) {
		$args = wp_parse_args( $args, array(
			'numeric' => false,             // use numeric array instead of associative
			'selected' => null,     // hack to make default blank
		) );

		return $this->_render_specific( $args );
	}

	protected function _set_value( &$args, $value ) {
		$args['selected'] = (array) $value;
	}

	protected function _render_specific( $args ) {
		extract( wp_parse_args( $args, array(
			'text' => false,
			'extra' => array()
		) ) );

		$options = array();
		if ( !is_array( $selected ) ){
			$selected = array();
		}

		foreach ( $choices as $value => $title ) {
			$options[] = array(
				'value' => $value,
				'selected' => in_array( $value, $selected ),
				'title' => $title
			);
		}
		$opts = '';
		foreach ( $options as $option ) {
			extract( $option );

			$opts .= emd_p2p_html( 'option', compact( 'value', 'selected' ), $title );
		}

		$extra['name'] = $name . '[]';
		$extra['multiple'] = 'multiple';
		$input = emd_p2p_html( 'select', $extra, $opts );

		return EmdScbFormField::add_label( $input, $desc, $desc_pos );
	}
}

class EmdScbRadiosField extends EmdScbSelectField {

	protected function _render_specific( $args ) {
		extract( $args );

		if ( array( 'foo' ) == $selected ) {
			// radio buttons should always have one option selected
			$selected = key( $choices );
		}

		$opts = '';
		foreach ( $choices as $value => $title ) {
			$single_input = EmdScbFormField::_checkbox( array(
				'name' => $name,
				'type' => 'radio',
				'value' => $value,
				'checked' => ( $value == $selected ),
				'desc' => $title,
				'desc_pos' => 'after'
			) );

			$opts .= str_replace( EmdScbForms::TOKEN, $single_input, $wrap_each );
		}

		return EmdScbFormField::add_desc( $opts, $desc, $desc_pos );
	}
}


class EmdScbMultipleChoiceField extends EmdScbFormField {

	public function validate( $value ) {
		return array_intersect( array_keys( $this->choices ), (array) $value );
	}

	protected function _render( $args ) {
		$args = wp_parse_args( $args, array(
			'numeric' => false,		// use numeric array instead of associative
			'checked' => null,
		) );

		extract( $args );

		if ( !is_array( $checked ) )
			$checked = array();

		$opts = '';
		foreach ( $choices as $value => $title ) {
			$single_input = EmdScbFormField::_checkbox( array(
				'name' => $name . '[]',
				'type' => 'checkbox',
				'value' => $value,
				'checked' => in_array( $value, $checked ),
				'desc' => $title,
				'desc_pos' => 'after'
			) );

			$opts .= str_replace( EmdScbForms::TOKEN, $single_input, $wrap_each );
		}

		return EmdScbFormField::add_desc( $opts, $desc, $desc_pos );
	}

	protected function _set_value( &$args, $value ) {
		$args['checked'] = (array) $value;
	}
}


class EmdScbSingleCheckboxField extends EmdScbFormField {

	public function validate( $value ) {
		return (bool) $value;
	}

	protected function _render( $args ) {
		$args = wp_parse_args( $args, array(
			'value' => true,
			'desc' => null,
			'checked' => false,
			'extra' => array(),
		) );

		foreach ( $args as $key => &$val )
			$$key = &$val;
		unset( $val );

		$extra['checked'] = $checked;

		if ( is_null( $desc ) && !is_bool( $value ) )
			$desc = str_replace( '[]', '', $value );

		return EmdScbFormField::_input_gen( $args );
	}

	protected function _set_value( &$args, $value ) {
		$args['checked'] = ( $value || ( isset( $args['value'] ) && $value == $args['value'] ) );
	}
}


class EmdScbCustomField implements EmdScbFormField_I {

	protected $args;

	function __construct( $args ) {
		$this->args = wp_parse_args( $args, array(
			'render' => 'var_dump',
			'sanitize' => 'wp_filter_kses',
		) );
	}

	public function __get( $key ) {
		return $this->args[ $key ];
	}

	public function __isset( $key ) {
		return isset( $this->args[ $key ] );
	}

	public function render( $value = null ) {
		return call_user_func( $this->render, $value, $this );
	}

	public function validate( $value ) {
		return call_user_func( $this->sanitize, $value, $this );
	}
}

