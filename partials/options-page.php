<?php
/**
 * Partioal for options page
 *
 * @package WP-Chatbot
 */

// must check that the user has the required capability
	if ( ! current_user_can( 'manage_options' ) ) {
wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	// variables for the field and option names
	$hidden_field_name = 'mt_submit_hidden';

	$opt_name = 'mt_favorite_color';

	$data_field_name = 'mt_favorite_color';

	// Read in existing option value from database
	$opt_val = get_option( $opt_name );

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if ( isset( $_POST[ $hidden_field_name ] ) && 'Y' == $_POST[ $hidden_field_name ] ) {
	// Read their posted value
	$opt_val = $_POST[ $data_field_name ];

	// Save the posted value in the database
	update_option( $opt_name, $opt_val );

	// Put a "settings saved" message on the screen
?>
<div class="updated"><p><strong><?php _e( 'settings saved.', 'menu-test' ); ?></strong></p></div>
<?php

	}

	// Now display the settings editing screen
	echo '<div class="wrap">';

	// header
	echo '<h2>' . __( 'Menu Test Plugin Settings', 'menu-test' ) . '</h2>';

	// settings form
	?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e( 'Favorite Color:', 'menu-test' ); ?>
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
</p><hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ) ?>" />
</p>

</form>
</div>
