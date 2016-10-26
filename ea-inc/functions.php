<?php
	$cwk_thumbimg = array(200, 999); // size of featured image in archive/category blog
	$cwk_postimg = array(200, 999); // size of featured image on single post.
	add_image_size( 'cwk-slider', 1420, 447, true ); // Slider
	
	add_action('after_setup_theme', ea_setup);
	/**  ea_setup
	*  init stuff that we have to init after the main theme is setup.
	* 
	*/
	function ea_setup() {
	 /* do stuff ehre. */
	 	/* add favicons for admin */
		add_action('login_head', 'add_favicon');
		add_action('admin_head', 'add_favicon');
	}
	
	function add_favicon() {
	  	$favicon_url = get_stylesheet_directory_uri() . '/images/admin-favicon.ico';
		echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
	}
	
	/* recommended from woothemes  -  new server with old mod_security settings */
	add_action( 'wp_enqueue_scripts', 'custom_frontend_scripts' );
	function custom_frontend_scripts() {

		global $post, $woocommerce;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_deregister_script( 'jquery-cookie' );
		wp_register_script( 'jquery-cookie', $woocommerce->plugin_url() . '/assets/js/jquery-cookie/jquery_cookie' . $suffix . '.js', array( 'jquery' ), '1.3.1', true );

	}

?>
