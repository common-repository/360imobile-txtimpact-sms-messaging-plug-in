<?php
// ======================================================================================
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
//
// This library is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.
// ======================================================================================
// @author     Simon Wheatley (http://simonwheatley.co.uk)
// @version    1.0
// @copyright  Copyright &copy; 2010 Simon Wheatley, All Rights Reserved
// @copyright  Some parts Copyright &copy; 2007 John Godley, All Rights Reserved
// ======================================================================================
// 1.0     - Initial release
// 1.01    - Added add_shortcode
// 1.10    - Added code to allow the base class to be used in a theme
// 1.2     - Truncate helper method, admin notices/errors, throw error if not provided
//           with name in setup method call, default $pluginfile to __FILE__, bugfix around
//           option key in delete_option method.
// ======================================================================================

/**
 * Wraps up several useful functions for WordPress plugins and provides a method to separate
 * display HTML from PHP code.
 *
 * <h4>Display Rendering</h4>
 *
 * The class uses a similar technique to Ruby On Rails views, whereby the display HTML is kept
 * in a separate directory and file from the main code.  A display is 'rendered' (sent to the browser)
 * or 'captured' (returned to the calling function).
 *
 * Template files are separated into two areas: admin and user.  Admin templates are only for display in
 * the WordPress admin interface, while user templates are typically for display on the site (although neither
 * of these are enforced).  All templates are PHP code, but are referred to without .php extension.
 *
 * The reason for this separation is that one golden rule of plugin creation is that someone will
 * always want to change the formatting and style of your output.  Rather than forcing them to
 * modify the plugin (bad), or modify files within the plugin (equally bad), the class allows
 * user templates to be overridden with files contained within the theme.
 *
 * An additional benefit is that it leads to code re-use, especially with regards to Ajax (i.e.
 * your display code can be called from many locations)
 *
 * @package WordPress plugin base library
 * @author Simon Wheatley
 * @copyright Copyright (C) Simon Wheatley (except where noted)
 **/
class TXTIMPACT_Plugin {
	/**
	 * The name of this plugin
	 *
	 * @var string
	 **/
	protected $name;
	/**
	 * The filepath to the directory containing this plugin
	 *
	 * @var string
	 **/
	protected $dir;
	/**
	 * The URL for the directory containing this plugin
	 *
	 * @var string
	 **/
	protected $url;
	/**
	 * The name of the directory containing this plugin, e.g. "my-plugin"
	 *
	 * @var string
	 **/
	protected $folder;
	/**
	 * Useful for switching between debug and compressed scripts.
	 *
	 * @var string
	 **/
	protected $suffix;
	/**
	 * Records the type of this class, either 'plugin' or 'theme'.
	 *
	 * @var string
	 **/
	protected $type;
	/**
	 * Note the name of the function to call when the theme is activated.
	 *
	 * @var string
	 **/
	protected $theme_activation_function;

	/**
	 * Initiate!
	 *
	 * @author Simon Wheatley
	 *
	 * @param string $name name.
	 *
	 * @throws exception Throws exception.
	 */
	public function setup( $name = '' ) {
		if ( ! $name ) {
			throw new exception( 'Please pass the name parameter into the setup method.' );
		}
		$this->name = $name;
		// Setup the dir and url for this plugin/theme.
		if ( stripos( __FILE__, 'themes' ) ) {
			// This is a theme.
			$this->type = 'theme';
			$this->dir  = get_stylesheet_directory();
			$this->url  = get_stylesheet_directory_uri();
		} elseif ( stripos( __FILE__, WP_PLUGIN_DIR ) !== false ) {
			// This is a plugin.
			$this->folder = rtrim( basename( dirname( __FILE__ ) ), '/' );
			$this->type   = 'plugin';
			$this->dir    = trailingslashit( WP_PLUGIN_DIR ) . $this->folder;
			$this->url    = plugins_url( $this->folder );
		} else {

			error_log( 'PLUGIN/THEME ERROR: Cannot find ' . WP_PLUGIN_DIR . ' or "themes" in ' . __FILE__ ); // phpcs:ignore
		}
		// Suffix for enqueuing.
		$this->suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';
		if ( is_admin() ) {
			// Admin notices.
			$this->add_action( 'admin_notices', '_admin_notices' );
		}
		$this->add_action( 'init', 'load_locale' );
	}

	/**
	 * Hook called to change the locale directory.
	 *
	 * @return void
	 * @author © John Godley
	 **/
	public function load_locale() {
		$locale = apply_filters( 'plugin_locale', get_locale(), $this->name );
		if ( empty( $locale ) ) {
			$locale = 'en_US';
		}
		$mofile = $this->dir( "/locale/$locale.mo" );
		load_textdomain( $this->name, $mofile );
	}

	/**
	 * Register a WordPress action and map it back to the calling object
	 *
	 * @param string $action Name of the action.
	 * @param string $function Function name (optional).
	 * @param int    $priority WordPress priority (optional).
	 * @param int    $accepted_args Number of arguments the function accepts (optional).
	 *
	 * @return void
	 * @author © John Godley
	 **/
	public function add_action( $action, $function = '', $priority = 10, $accepted_args = 1 ) {
		if ( null === $priority ) {
			$priority = 10;
		}
		add_action( $action, array( &$this, ( '' === $function ) ? $action : $function ), $priority, $accepted_args );
	}

	/**
	 * Register a WordPress filter and map it back to the calling object
	 *
	 * @param string $filter Name of the action.
	 * @param string $function Function name (optional).
	 * @param int    $priority WordPress priority (optional).
	 * @param int    $accepted_args Number of arguments the function accepts (optional).
	 *
	 * @return void
	 * @author © John Godley
	 **/
	public function add_filter( $filter, $function = '', $priority = 10, $accepted_args = 1 ) {
		add_filter( $filter, array( &$this, $function == '' ? $filter : $function ), $priority, $accepted_args );
	}

	/**
	 * Special activation function that takes into account the plugin directory
	 *
	 * @param string $pluginfile The plugin file location (i.e. __FILE__).
	 * @param string $function Optional function name, or default to 'activate'.
	 *
	 * @return void
	 * @author © John Godley
	 **/
	public function register_activation( $pluginfile = __FILE__, $function = '' ) {
		if ( $this->type === 'plugin' ) {
			add_action( 'activate_' . basename( dirname( $pluginfile ) ) . '/' . basename( $pluginfile ), array( &$this, $function == '' ? 'activate' : $function ) );
		} elseif ( $this->type === 'theme' ) {
			$this->theme_activation_function = ( $function ) ? $function : 'activate';
			add_action( 'load-themes.php', array( & $this, 'theme_activation' ) );
		}
	}

	/**
	 * Hack to catch theme activation. We hook the load-themes.php action, look for the
	 * "activated" GET param and make a big fat assumption if we find it.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function theme_activation() {
		$activated = (bool) @ $_GET['activated']; // phpcs:ignore
		if ( ! $activated ) {
			return;
		}
		if ( ! $this->theme_activation_function ) {
			return;
		}
		// Looks like the theme might just have been activated, call the registered function.
		$this->{$this->theme_activation_function}();
	}

	/**
	 * Special deactivation function that takes into account the plugin directory
	 *
	 * @param string $pluginfile The plugin file location (i.e. __FILE__).
	 * @param string $function Optional function name, or default to 'deactivate'.
	 *
	 * @return void
	 * @author © John Godley
	 **/
	function register_deactivation( $pluginfile, $function = '' ) {
		add_action( 'deactivate_' . basename( dirname( $pluginfile ) ) . '/' . basename( $pluginfile ), array( &$this, ( '' === $function ) ? 'deactivate' : $function ) );
	}

	/**
	 * Renders a template, looking first for the template file in the theme directory
	 * and afterwards in this plugin's /theme/ directory.
	 *
	 * @param string     $template_file file.
	 * @param array|null $vars vars.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function render( $template_file, $vars = null ) {
		// Maybe override the template with our own file.
		$template_file = $this->locate_template( $template_file );
		// Ensure we have the same vars as regular WP templates.
		global $posts, $post, $wp_did_header, $wp_did_template_redirect, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
		if ( is_array( $wp_query->query_vars ) ) {
			extract( $wp_query->query_vars, EXTR_SKIP );
		}
		// Plus our specific template vars.
		if ( is_array( $vars ) ) {
			extract( $vars );
		}
		require $template_file;
	}

	/**
	 * Renders an admin template from this plugin's /templates-admin/ directory.
	 *
	 * @param string     $template_file file.
	 * @param array|null $vars vars.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function render_admin( $template_file, $vars = null ) {
		// Plus our specific template vars.
		if ( is_array( $vars ) ) {
			extract( $vars );
		}
		// Try to render.
		if ( file_exists( $this->dir( "templates-admin/$template_file" ) ) ) {
			require $this->dir( "templates-admin/$template_file" );
		} else {
			// translators: file path.
			$msg = sprintf( __( 'This plugin admin template could not be found: %s' ), $this->dir( "templates-admin/$template_file" ) );
			error_log( "Plugin template error: $msg" ); // phpcs:ignore
			error_log( print_r( $this->dir( "templates-admin/$template_file" ) ) ); // phpcs:ignore
			echo "<p style='background-color: #ffa; border: 1px solid red; color: #300; padding: 10px;'>$msg</p>";
		}
	}

	/**
	 * Returns a section of user display code, returning the rendered markup.
	 *
	 * @param string $template_file Name of the admin file (without extension).
	 * @param string $vars Array of variable name=>value that is available to the display code (optional).
	 *
	 * @return string
	 * @author © John Godley
	 **/
	protected function capture( $template_file, $vars = null ) {
		ob_start();
		$this->render( $template_file, $vars );
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Returns a section of user display code, returning the rendered markup.
	 *
	 * @param string $template_file Name of the admin file (without extension).
	 * @param string $vars Array of variable name=>value that is available to the display code (optional).
	 *
	 * @return string
	 *
	 * @author © John Godley
	 **/
	protected function capture_admin( $template_file, $vars = null ) {
		ob_start();
		$this->render_admin( $template_file, $vars );
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Hooks the WP admin_notices action to render any notices
	 * that have been set with the set_admin_notice method.
	 *
	 * @author Simon Wheatley
	 **/
	public function _admin_notices() {
		$notices = $this->get_option( 'admin_notices' );
		$errors  = $this->get_option( 'admin_errors' );
		if ( $errors ) {
			foreach ( $errors as $error ) {
				$this->render_admin_error( $error );
			}
		}
		$this->delete_option( 'admin_errors' );
		if ( $notices ) {
			foreach ( $notices as $notice ) {
				$this->render_admin_notice( $notice );
			}
		}
		$this->delete_option( 'admin_notices' );
	}

	/**
	 * Echoes some HTML for an admin notice.
	 *
	 * @param string $notice The notice.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function render_admin_notice( $notice ) {
		echo "<div class='updated'><p>$notice</p></div>";
	}

	/**
	 * Echoes some HTML for an admin error.
	 *
	 * @param string $error The error.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function render_admin_error( $error ) {
		echo "<div class='error'><p>$error</p></div>";
	}

	/**
	 * Sets a string as an admin notice.
	 *
	 * @param string $msg A *localised* admin notice message.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function set_admin_notice( $msg ) {
		$notices   = (array) $this->get_option( 'admin_notices' );
		$notices[] = $msg;
		$this->update_option( 'admin_notices', $notices );
	}

	/**
	 * Sets a string as an admin error.
	 *
	 * @param string $msg A *localised* admin error message.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function set_admin_error( $msg ) {
		$errors   = (array) $this->get_option( 'admin_errors' );
		$errors[] = $msg;
		$this->update_option( 'admin_errors', $errors );
	}

	/**
	 * Takes a filename and attempts to find that in the designated plugin templates
	 * folder in the theme (defaults to main theme directory, but uses a custom filter
	 * to allow theme devs to specify a sub-folder for all plugin template files using
	 * this system).
	 *
	 * Searches in the STYLESHEETPATH before TEMPLATEPATH to cope with themes which
	 * inherit from a parent theme by just overloading one file.
	 *
	 * @param string $template_file A template filename to search for.
	 *
	 * @author Simon Wheatley
	 *
	 * @return mixed
	 **/
	protected function locate_template( $template_file ) {
		$located = '';
		$sub_dir = apply_filters( 'sw_plugin_tpl_dir', '' );
		if ( $sub_dir ) {
			$sub_dir = trailingslashit( $sub_dir );
		}

		$path          = get_stylesheet_directory();
		$template_path = get_template_directory();

		// If there's a tpl in a (child theme or theme with no child).
		if ( file_exists( $path . "/$sub_dir" . $template_file ) ) {
			return $path . "/$sub_dir" . $template_file;
		} // If there's a tpl in the parent of the current child theme
		elseif ( file_exists( $template_path . "/$sub_dir" . $template_file ) ) {
			return $template_path . "/$sub_dir" . $template_file;
		} // Fall back on the bundled plugin template (N.B. no filtered subfolder involved)
		elseif ( file_exists( $this->dir( "templates/$template_file" ) ) ) {
			return $this->dir( "templates/$template_file" );
		}

		// Oh dear. We can't find the template.
		// translators: file path.
		$msg = sprintf( __( 'This plugin template could not be found: %s' ), $this->dir( "templates/$template_file" ) );
		error_log( "Template error: $msg" ); // phpcs:ignore
		echo "<p style='background-color: #ffa; border: 1px solid red; color: #300; padding: 10px;'>$msg</p>";
	}

	/**
	 * Register a WordPress meta box
	 *
	 * @param string $id ID for the box, also used as a function name if none is given.
	 * @param string $title Title for the box.
	 * @param string $function Function name (optional).
	 * @param int    $page The type of edit page on which to show the box (post, page, link).
	 * @param string $context e.g. 'advanced' or 'core' (optional).
	 * @param string $priority Priority, rough effect on the ordering (optional).
	 * @param mixed  $args Some arguments to pass to the callback function as part of a larger object (optional).
	 *
	 * @return void
	 * @author © John Godley
	 **/
	public function add_meta_box( $id, $title, $function = '', $page, $context = 'advanced', $priority = 'default', $args = null ) {
		require_once ABSPATH . 'wp-admin/includes/template.php';
		add_meta_box( $id, $title, array( &$this, ( '' === $function ) ? $id : $function ), $page, $context, $priority, $args );
	}

	/**
	 * Add hook for shortcode tag.
	 *
	 * There can only be one hook for each shortcode. Which means that if another
	 * plugin has a similar shortcode, it will override yours or yours will override
	 * theirs depending on which order the plugins are included and/or ran.
	 *
	 * @param string   $tag Shortcode tag to be searched in post content.
	 * @param callable $function Hook to run when shortcode is found.
	 */
	protected function add_shortcode( $tag, $function = null ) {
		add_shortcode( $tag, array( &$this, ( '' === $function ) ? $tag : $function ) );
	}

	/**
	 * Returns the filesystem path for a file/dir within this plugin.
	 *
	 * @param string $path The path within this plugin, e.g. '/js/clever-fx.js'.
	 *
	 * @return string Filesystem path
	 * @author Simon Wheatley
	 **/
	protected function dir( $path ) {
		return trailingslashit( $this->dir ) . trim( $path, '/' );
	}

	/**
	 * Returns the URL for for a file/dir within this plugin.
	 *
	 * @param string $path The path within this plugin, e.g. '/js/clever-fx.js'.
	 *
	 * @return string URL
	 * @author Simon Wheatley
	 **/
	protected function url( $path ) {
		return trailingslashit( $this->url ) . trim( $path, '/' );
	}

	/**
	 * Gets the value of an option named as per this plugin.
	 *
	 * @return mixed Whatever
	 * @author Simon Wheatley
	 **/
	protected function get_all_options() {
		return get_option( $this->name, array() );
	}

	/**
	 * Sets the value of an option named as per this plugin.
	 *
	 * @param string $value value.
	 *
	 * @return mixed Whatever
	 * @author Simon Wheatley
	 **/
	protected function update_all_options( $value ) {
		return update_option( $this->name, $value );
	}

	/**
	 * Gets the value from an array index on an option named as per this plugin.
	 *
	 * @param string $key A string.
	 * @param string $value A string.
	 *
	 * @return mixed Whatever
	 * @author Simon Wheatley
	 **/
	public function get_option( $key, $value = null ) {
		$option = get_option( $this->name, array() );
		if ( ! isset( $option[ $key ] ) ) {
			return $value;
		}

		return $option[ $key ];
	}

	/**
	 * Sets the value on an array index on an option named as per this plugin.
	 *
	 * @param string $key A string.
	 * @param mixed  $value Whatever.
	 *
	 * @return bool False if option was not updated and true if option was updated.
	 * @author Simon Wheatley
	 **/
	protected function update_option( $key, $value ) {
		$option = get_option( $this->name, array() );
		if ( ! is_array( $option ) ) {
			$option = array();
		}
		$option[ $key ] = $value;

		return update_option( $this->name, $option );
	}

	/**
	 * Deletes the array index on an option named as per this plugin.
	 *
	 * @param string $key A string.
	 *
	 * @return bool False if option was not updated and true if option was updated.
	 * @author Simon Wheatley
	 **/
	protected function delete_option( $key ) {
		$option = get_option( $this->name, array() );
		if ( isset( $option[ $key ] ) ) {
			unset( $option[ $key ] );
		}

		return update_option( $this->name, $option );
	}

	/**
	 * Echoes out some JSON indicating that stuff has gone wrong.
	 *
	 * @param string $msg The error message.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function ajax_die( $msg ) {
		$data = array(
			'msg'     => $msg,
			'success' => false,
		);
		echo wp_json_encode( $data );
		// N.B. No 500 header.
		exit;
	}

	/**
	 * Truncates a string in a human friendly way.
	 *
	 * @param string $str The string to truncate.
	 * @param int    $num_words The number of words to truncate to.
	 *
	 * @return string The truncated string
	 * @author Simon Wheatley
	 **/
	protected function truncate( $str, $num_words ) {
		$str   = strip_tags( $str );
		$words = explode( ' ', $str );
		if ( count( $words ) > $num_words ) {
			$k             = $num_words;
			$use_dotdotdot = 1;
		} else {
			$k             = count( $words );
			$use_dotdotdot = 0;
		}

		$words   = array_slice( $words, 0, $k );
		$excerpt = trim( join( ' ', $words ) );

		$excerpt .= ( $use_dotdotdot ) ? '…' : '';

		return $excerpt;
	}
} // END *_Plugin class
