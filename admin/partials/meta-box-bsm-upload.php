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
<div id="bsm-add-media-button">
<a href="#" class="button insert-media add_media" data-editor="content" title="Add Media">
    <span class="wp-media-buttons-icon"></span> Upload Background Images
</a>
</div>
<div id="bsm-add-meida-instruction">
	<h3>Instructions</h3>
	<p>1. Click "Upload Background Images" to add images.<br />
	   2. Click "Publish" or "Update" to save your slider!<br />
	   3. Go to the slider "Settings" to apply it globally, or visit your pages individually and choose what slider to display on each page.
	</p>
</div>
<div id="bsm-images-container">
<?php
	$images = get_attached_media( 'image' );
	$uploads = wp_upload_dir(); 
	$html = '';
	foreach( $images as $image ){
		$image_id = $image->ID;
		$image_file = get_post_meta( $image_id );
		$image_meta = wp_get_attachment_metadata( $image_id );
		$html .= '
			<div class="bsm-image-container">
				<img src="'. $uploads['baseurl'] . '/' . $image_file['_wp_attached_file'][0] .'" class="bsm-image bsm-thumbnail" />
				<label class="bsm-image-title">' . $image->post_title . '</label>
			</div>
		';
	}
	echo $html;
 ?>
</div>