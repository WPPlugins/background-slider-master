<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://icanwp.com/plugins/background-slider-master/
 * @since      1.0.0
 *
 * @package    Background_Slider_Master
 * @subpackage Background_Slider_Master/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Background_Slider_Master
 * @subpackage Background_Slider_Master/public
 * @author     iCanWP Team, Sean Roh, Chris Couweleers
 */
class Background_Slider_Master_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_scripts_styles() {
		$slider_id = 0;
		if (is_single() || is_page()) {
			$slider_id = get_post_meta( get_the_ID(), '_bsm_selected_gallery', true );
			if ( $slider_id < 1) {
				$slider_id = get_option( 'bsm_select_gallery_settings_field' );
			}
		} else {
			$slider_id = get_option( 'bsm_select_gallery_settings_field' );
		}
		if ( $slider_id !== 0 ) {
			$slider_images = get_attached_media( 'image', $slider_id );
			if( count($slider_images) > 0 ){
				wp_register_script( $this->plugin_name . '_bsm_script', plugin_dir_url( __FILE__ ) . 'js/background-slider-master-public.js', array('jquery','jquery-effects-core'), $this->version, true );
				wp_enqueue_script( $this->plugin_name . '_bsm_script' );
				$bsm_loc = array( 'bsm_plugin_url' => plugin_dir_url( __FILE__ ) );
				wp_localize_script( $this->plugin_name . '_bsm_script', 'bsm_loc', $bsm_loc);
				
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/background-slider-master-public.css', array(), $this->version, 'all' );
				add_action( 'wp_head', array( $this, 'load_bsm' ) );
			}
		}
	}

	public function load_bsm(){
		$slider_id = 0;
		if (is_single() || is_page()) {
			$slider_id = get_post_meta( get_the_ID(), '_bsm_selected_gallery', true );
			if ( $slider_id < 1) {
				$slider_id = get_option( 'bsm_select_gallery_settings_field' );
			}
		} else {
			$slider_id = get_option( 'bsm_select_gallery_settings_field' );
		}

		$slider_images = get_attached_media( 'image', $slider_id );
		
		$first_image = reset($slider_images); // get the first element in the array
		$first_image_src = wp_get_attachment_image_src( $first_image -> ID, 'full' );
		
		$html = '
			<a href="#" class="BSMnextImageBtn" title="next"></a>
			<a href="#" class="BSMprevImageBtn" title="previous"></a>
			<div id="bsm-bg">
				<img src="'. $first_image_src[0] .'" alt="'. $first_image -> post_title .'" title="'. $first_image -> post_title .'" id="bsm-bgimg" />
			</div>
			<div id="bsm-preloader">
				<img src="'. plugin_dir_url( __FILE__ ) .'assets/ajax-loader_dark.gif" width="32" height="32" />
			</div>
			
			<div id="bsm-toolbar">
				<a href="#">
					<img src="'. plugin_dir_url( __FILE__ ) .'assets/toolbar_fs_icon.png" width="50" height="50" />
				</a>
			</div>
			<div id="bsm-thumbnails-wrapper">
				<div id="bsm-outer-container">
					<div class="thumbScroller">
						<div class="container">
		';
		
		foreach ($slider_images as $image){
			$image_id = $image -> ID;
			$image_file = wp_get_attachment_image_src( $image_id, 'full' );
			$image_thumb = wp_get_attachment_image_src( $image_id, 'thumbnail' );
			$html .= '<div class="content">
							<div>
								<a href="'. $image_file[0] .'"><img src="'. $image_thumb[0] .'" title="'. $image -> post_title .'" alt="'. $image -> post_title .'" class="thumb" /></a>
							</div>
						</div>
			';
							
		}
		
		$html .= '
						</div>
					</div>
				</div>
			</div>
		';
		
		echo $html;
	}

}
