<?php
/*
Plugin Name: Auto Post Title
Plugin URI: http://toolstack.com/auto-post-title
Description: Automatically set published post titles to the publish date if one does not exist.
Version: 1.0
Author: Greg Ross
Author URI: http://toolstack.com
Tags: post title
License: GPLv2 or later
*/

define( 'AutoPostTitle', '1.0' );

// Grabs the inserted post data so it can be modify.
add_filter( 'wp_insert_post_data' , 'apt_modify_post_title' , '99', 1 );

// Adds a link to settings in the plugin page.
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'apt_settings_links', 10, 2 );

// Add the reading section on admin init.
add_action('admin_init', 'apt_add_reading_section' );

// Modify the post title when publishing the post.
function apt_modify_post_title( $data ) {
	// Check we're in a post that's being published in the future and has ''/'Auto Draft' for the title.
  	if( $data['post_type'] == 'post' && ( $data['post_status'] == 'publish' || $data['post_status'] == 'future' ) && ( $data['post_title'] == '' || $data['post_title'] == 'Auto Draft' ) ) {

  		// Get the WordPress default date format.
		$date_format = get_option( 'date_format', 'F j, Y' );

  		// Get the title format, use the WordPress date format as the default if not set.
		$title_format = get_option( 'apt_title_format', $date_format );

  		// Set the title to the current date.
    	$data['post_title'] = date( $title_format, strtotime( $data['post_date'] ) );
 	}

  	return $data; // Returns the modified data.
}

// Display the settings section on the reading page.
function apt_render_reading_section() {
	$date_format = get_option( 'date_format', 'F j, Y' );
	$title_format = get_option( 'apt_title_format', '' );
	$example = date( '\N\e\w\s \f\o\r l F d, Y' );
	$system = date( $date_format );

	echo sprintf( __( 'Use the following format to create a post title, you may use any of the standard date/time formating.  If you want to include plain text in the title format, you must escape each character with a <kbd>\\</kbd>.  For example <kbd>\N\e\w\s \f\o\r l F d, Y</kbd> will product a title of <kbd>%1s</kbd>.', 'auto-post-title' ), $example );
	echo '<br><br>';
	echo __( sprintf( 'A blank value will use the system default date format of <kbd>%1s</kbd> (%2s).', $date_format, $system ), 'auto-post-title' );
	echo '<br><br>';
	echo __( '<b>Note</b>: The title will not be set until a post is set to published, or publish in the future.', 'auto-post-title' );
?>
<table class="form-table" role="presentation">
<tbody><tr>
<th scope="row"><label for="apt_title_format"><?php echo __( 'Title Template', 'auto-post-title' ); ?></label></th>
<td><input name="apt_title_format" type="text" id="apt_title_format" placeholder="<?php echo __( '\N\e\w\s \f\o\r l F d, Y', 'auto-post-title' ); ?>" value="<?php echo $title_format; ?>" class="regular-text code"></td>
</tr><tr>
<th scope="row"></th>
<td><a href="https://wordpress.org/support/article/formatting-date-and-time/"><?php echo __( 'Documentation on date and time formatting.', 'auto-post-title' ); ?></a></td>
</tbody></table>
<?php

}

// Setup the rest of the plugin.
function apt_add_reading_section() {
	// Register our setting that we're adding to the writing screen.
	register_setting( 'writing', 'apt_title_format' );

	// Register the new section for the writing screen.
	add_settings_section( 'Auto Post Title', 'Auto Post Title', 'apt_render_reading_section', 'writing' );
}

function apt_load_langauge() {
	load_plugin_textdomain( 'auto-post-title', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	__( 'Auto Post Title', 'auto-post-title' );
	__( 'Automatically set published post titles to the publish date if one does not exist.', 'auto-post-title' );
}

// Adds a settings link to the plugins list in WordPress.
function apt_settings_links( $links, $file ) {
	array_unshift( $links, '<a href="' . admin_url( 'options-writing.php' ) . '">' . __( 'Settings', 'auto-post-title' ) . '</a>' );

	return $links;
}

