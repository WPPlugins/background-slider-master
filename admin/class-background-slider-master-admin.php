<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://icanwp.com/plugins/background-slider-master/
 * @since      1.0.0
 *
 * @package    Background_Slider_Master
 * @subpackage Background_Slider_Master/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Background_Slider_Master
 * @subpackage Background_Slider_Master/admin
 * @author     iCanWP Team, Sean Roh, Chris Couweleers
 */
class Background_Slider_Master_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'save_post', array( $this, 'bsm_save_meta_boxes' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Background_Slider_Master_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Background_Slider_Master_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name . 'admin-css', plugin_dir_url( __FILE__ ) . 'css/background-slider-master-admin.css', array(), $this->version, 'all' );

	}

	public function bsm_add_admin_menu(){
		add_menu_page(
			'Background Slider Master', // The title to be displayed on this menu's corresponding page
			'Background Slider', // The text to be displayed for this actual menu item
			'manage_options', // Which type of users can see this menu
			'bsm_admin_menu', // The unique ID - that is, the slug - for this menu item
			'', // The name of the function to call when rendering this menu's page
			plugin_dir_url( __FILE__ ) . 'assets/admin-icon.png', // icon url
			138.831 // position
		);
		add_submenu_page(
			'bsm_admin_menu',                  // Register this submenu with the menu defined above
			'Background Slider Master Settings',          // The text to the display in the browser when this menu item is active
			'Settings',                  // The text for this menu item
			'manage_options',            // Which type of users can see this menu
			'bsm_settings_menu',          // The unique ID - the slug - for this menu item
			array($this, 'display_bsm_settings_menu_page')   // The function used to render this menu's page to the screen
		);
	}
	public function bsm_init_options(){
		add_settings_section(
			'bsm_select_gallery_settings_section', // ID used to identify this section and with which to register options
			'Main Settings',
			'',
			'bsm_settings_menu' // Page on which to add this section of options
		);
		add_settings_field( 
			'bsm_select_gallery_settings_field',
			'Choose global background slider image set',
			array($this, 'callback_bsm_select_gallery_settings_field'),
			'bsm_settings_menu',
			'bsm_select_gallery_settings_section',
			array(
				'Check to allow the use of shortcode in the text widget. <br /><span class="ch_warning"><strong>WARNING:</strong> This will allow the use of any shortcode from the text widget globally.</span>'
			)
		);
		register_setting(
			'bsm_settings_menu',
			'bsm_select_gallery_settings_field'
		);
	}
	public function bsm_register_custom_post_type(){
		$labels = array(
			'name'               => 'Background Slides',
			'singular_name'      => 'Background Slide',
			'menu_name'          => 'Background Slider Master',
			'name_admin_bar'     => 'Background Slider Master',
			'add_new'            => 'Add New Background Slide Set',
			'add_new_item'       => 'Add New Background Slide',
			'new_item'           => 'New Background Slide Set',
			'edit_item'          => 'Edit Background Slide Set',
			'view_item'          => 'View Background Slide Set',
			'all_items'          => 'Background Slide Sets',
			'search_items'       => 'Search Background Slide Set',
			'parent_item_colon'  => 'Parent Background Slide Set:',
			'not_found'          => 'No Background Slide Set Found',
			'not_found_in_trash' => 'No Background Slide Set Found in Trash.'
		);

		$args = array(
			'labels'             => $labels,
			'description'        => 'Background Slider Master Slide Set',
			'public'             => true,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'bsm_admin_menu',
			'menu_position'			=> 20.41,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'thumbnail')
		);

		register_post_type( 'bsm-gallery-slides', $args );
	}
	public function bsm_add_meta_boxes($post_type){
		add_meta_box(
			'bsm_files', 
			'Background Slider Master Images', 
			array($this, 'callback_add_meta_box'),
			'bsm-gallery-slides'
		);
		
		$default_post_types = array( 'post', 'page' );
		if ( in_array( $post_type, $default_post_types )) {
			add_meta_box(
			'bsm_gallery',
			'Select Background Slider Master Gallery',
			array($this, 'callback_add_meta_bsm_selector'),
			$post_type,
			'side',
			'default'
			);
		}
	}
	public function bsm_save_meta_boxes($post_id){
		/*
		* We need to verify this came from the our screen and with proper authorization,
		* because save_post can be triggered at other times.
		*/

		// Check if our nonce is set.
		if ( ! isset( $_POST['bsm_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['bsm_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'bsm_custom_box' ) )
		return $post_id;

		// If this is an autosave, our form has not been submitted,
		//     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) )
		return $post_id;

		} else {

		if ( ! current_user_can( 'edit_post', $post_id ) )
		return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		$mydata = sanitize_text_field( $_POST['bsm_field_select_slider'] );

		$bsm_selected = sanitize_text_field( $_POST['bsm_field_select_gallery'] );
		// Update the meta field.
		
		update_post_meta( $post_id, '_bsm_selected_gallery', $bsm_selected );
		
		
		update_post_meta( $post_id, '_bsm_test_key', $mydata );
	}
	public function callback_add_meta_bsm_selector($post){
		
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'bsm_custom_box', 'bsm_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$value = get_post_meta( $post->ID, '_bsm_test_key', true );
		$bsm_selected_id = get_post_meta( $post->ID, '_bsm_selected_gallery', true );
		
		$bsm_slide_set_option = array(
			'post_status' => 'publish',
			'post_type' => 'bsm-gallery-slides',
			'post_per_page' => -1,
			'order' => 'ASC'
		);
		$bsm_slide_set = new WP_Query( $bsm_slide_set_option );
		
		$html = '<select name="bsm_field_select_gallery" id="bsm_field_select_gallery">';
		$html .= '<option value="0">Use Global Setting</option>';
		while ($bsm_slide_set -> have_posts()){
			$bsm_slide_set -> the_post();
			$html .= '<option value="'. get_the_ID() . '" ';
			if( get_the_ID() === intval($bsm_selected_id) ){
				$html .= ' selected="selected" >';
			} else {
				$html .= '>';
			}
			if( get_the_title() === '' ){
				$html .= '(no title)</option>';
			} else {
				$html .= esc_attr(get_the_title()) . '</option>';
			}
		}
		$html .= '</select>';
		wp_reset_postdata();
		echo $html;
	}
	public function callback_add_meta_box($post){
		require_once('partials/meta-box-bsm-upload.php');
	}
	
	public function display_bsm_settings_menu_page(){
		require_once('partials/menu-page-bsm-settings.php');
	}
	
	public function callback_bsm_select_gallery_settings_field(){
		$selected = get_option('bsm_select_gallery_settings_field');
		
		$bsm_slide_set_option = array(
			'post_status' => 'publish',
			'post_type' => 'bsm-gallery-slides',
			'post_per_page' => -1,
			'order' => 'ASC'
		);
		$bsm_slide_set = new WP_Query( $bsm_slide_set_option );
		
		$html = '<select name="bsm_select_gallery_settings_field" id="bsm_select_gallery_settings_field">';
		$html .= '<option value="0">Disabled</option>';
		while ($bsm_slide_set -> have_posts()){
			$bsm_slide_set -> the_post();
			$html .= '<option value="'. get_the_ID() . '" ';
			if( get_the_ID() === intval($selected) ){
				$html .= ' selected="selected" >';
			} else {
				$html .= '>';
			}
			if( get_the_title() === '' ){
				$html .= '(no title)</option>';
			} else {
				$html .= esc_attr( get_the_title() ) . '</option>';
			}
		}
		$html .= '</select>';
		wp_reset_postdata();
		echo $html;
	}
}
