<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://icanwp.com/plugins/background-slider-master/
 * @since      1.0.0
 *
 * @package    Background_Slider_Master
 * @subpackage Background_Slider_Master/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h2>Background Slider - Settings</h2>
<form class="bsm-settings" method="post" action="options.php"> 
<?php 
	settings_fields( 'bsm_settings_menu' );
	do_settings_sections( 'bsm_settings_menu' ); 
	submit_button(); 
?>
</form>
<h3>FAQ &amp; Troubleshooting</h3>
<h4>Q1. Background Slider image is covered behind other elements.</h4>
<p>
A. The background images are loaded as the first element under the  &lt;head&gt; section, but it is given z-index value of 0. Please make sure there are no html elements, such as &lt;body&gt; or &lt;div&gt; with background color or image specified in CSS.
</p>