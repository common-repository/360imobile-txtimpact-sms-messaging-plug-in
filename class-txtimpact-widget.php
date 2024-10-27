<?php
/*
Copyright 2012 TXTImpact
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Extends the WP_Widget base class in ways I find handy.
 */
class TXTIMPACT_Widget extends WP_Widget {
	// FORM TEMPLATE FUNCTIONS.
	/**
	 * Fields for the admin form.
	 *
	 * @param string $label
	 * @param string $var
	 * @param string $value
	 * @param bool $note
	 * @param string $class
	 */
	public function input_text( $label, $var, $value, $note = false, $class = 'widefat' ) {
		$value = esc_attr( $value );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $var ) ); ?>"><?php echo esc_html( $label ); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $var ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $var ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>"/> </label>
			<?php if ( $note ) { ?>
				<br/>
				<small><?php echo esc_html( $note ); ?></small>
			<?php } ?>
		</p>
		<?php
	}

	/**
	 * @param string $label
	 * @param string $var
	 * @param string $value
	 * @param bool $note
	 * @param string $class
	 */
	public function textarea( $label, $var, $value, $note = false, $class = 'widefat' ) {
		$value = esc_textarea( $value );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $var ) ); ?>"><?php echo esc_html( $label ); ?>
				<textarea class="<?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $var ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $var ) ); ?>"><?php echo esc_html( $value ); ?></textarea>
			</label>
			<?php if ( $note ) { ?>
				<br/>
				<small><?php echo esc_html( $note ); ?></small>
			<?php } ?>
		</p>
		<?php
	}

	/**
	 * @param        $label
	 * @param        $var
	 * @param        $value
	 * @param bool   $note
	 * @param string $class
	 */
	public function input_conversational_mini_text( $label, $var, $value, $note = false, $class = 'widefat' ) {
		$value = esc_attr( $value );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $var ) ); ?>"><?php echo esc_html( $label ); ?></label>
			<input size="3" id="<?php echo esc_attr( $this->get_field_id( $var ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $var ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>"/><br/>
			<?php if ( $note ) { ?>
				<small><?php echo esc_html( $note ); ?></small>
			<?php } ?>
		</p>
		<?php
	}

	/**
	 * @param      $label
	 * @param      $var
	 * @param      $options
	 * @param      $selected
	 * @param bool $note
	 */
	public function input_select( $label, $var, $options, $selected, $note = false ) {
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $var ) ); ?>"><?php echo esc_html( $label ); ?> <br/>
				<select name="<?php echo esc_attr( $this->get_field_name( $var ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( $var ) ); ?>">
					<option value="">-- <?php esc_html_e( 'Please select a module', 'wmlib' ); ?> --</option>
					<?php foreach ( $options as $i => $option ) { ?>
						<option value="<?php echo esc_attr( $i ); ?>" <?php echo ( $i === $selected ) ? 'selected="selected"' : ''; ?>><?php echo esc_html( $option ); ?></option>
					<?php } ?>
				</select>
			</label>
			<?php if ( $note ) { ?>
				<br/>
				<small><?php echo esc_html( $note ); ?></small>
			<?php } ?>
		</p>
		<?php
	}

	/**
	 * @param        $label
	 * @param        $var
	 * @param        $value
	 * @param bool   $note
	 * @param string $class
	 */
	public function input_checkbox( $label, $var, $value, $note = false, $class = 'widefat' ) {
		$checked = ( $value ) ? ' checked="checked" ' : '';
		?>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( $var ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $var ) ); ?>" <?php echo esc_attr( $checked ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( $var ) ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php if ( $note ) { ?>
				<br/>
				<small><?php echo esc_html( $note ); ?></small>
			<?php } ?>
		</p>
		<?php
	}

	/**
	 * @param $before_widget
	 * @param $additional_classes
	 *
	 * @return mixed
	 */
	protected function add_classes( $before_widget, $additional_classes ) {
		$main_widget_class = $this->widget_options['classname'];
		$classes           = "$main_widget_class $additional_classes ";
		$before_widget     = str_replace( $main_widget_class, $classes, $before_widget );

		return $before_widget;
	}
}
