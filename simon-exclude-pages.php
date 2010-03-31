<?php
/*
Exclude Pages from Navigation
http://wordpress.org/extend/plugins/exclude-pages/


Provides a checkbox on the editing page which you can check to exclude pages from the primary navigation. IMPORTANT NOTE: This will remove the pages from any "consumer" side page listings, which may not be limited to your page navigation listings.

Version: 1.8

Simon Wheatley
http://simonwheatley.co.uk/wordpress/

Copyright 2007 Simon Wheatley

This script is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

// Full filesystem path to this dir
define('sdep_PLUGIN_DIR', dirname(__FILE__));

// Option name for exclusion data
define('sdep_OPTION_NAME', 'ep_exclude_pages');
// Separator for the string of IDs stored in the option value
define('sdep_OPTION_SEP', ',');

// Take the pages array, and return the pages array without the excluded pages
// Doesn't do this when in the admin area
function sdep_exclude_pages( $pages )
{
	// If the URL includes "wp-admin", just return the unaltered list
	// This constant, WP_ADMIN, only came into WP on 2007-12-19 17:56:16 rev 6412, i.e. not something we can rely upon unfortunately.
	// May as well check it though.
	// Also check the URL... let's hope they haven't got a page called wp-admin (probably not)
	// SWTODO: Actually, you can create a page with an address of wp-admin (which is then inaccessible), I consider this a bug in WordPress (which I may file a report for, and patch, another time).
	$bail_out = ( ( defined( 'WP_ADMIN' ) && WP_ADMIN == true ) || ( strpos( $_SERVER[ 'PHP_SELF' ], 'wp-admin' ) !== false ) );
	$bail_out = apply_filters( 'sdep_admin_bail_out', $bail_out );
	if ( $bail_out ) return $pages;
	$excluded_ids = sdep_get_excluded_ids();
	$length = count($pages);
	// Ensure we catch all descendant pages, so that if a parent
	// is hidden, it's children are too.
	for ( $i=0; $i<$length; $i++ ) {
		$page = & $pages[$i];
		// If one of the ancestor pages is excluded, add it to our exclude array
		if ( sdep_ancestor_excluded( $page, $excluded_ids, $pages ) ) {
			// Can't actually delete the pages at the moment, 
			// it'll screw with our recursive search.
			// For the moment, just tag the ID onto our excluded IDs
			$excluded_ids[] = $page->ID;
		}
	}

	// Ensure the array only has unique values
	$delete_ids = array_unique( $excluded_ids );
	
	// Loop though the $pages array and actually unset/delete stuff
	for ( $i=0; $i<$length; $i++ ) {
		$page = & $pages[$i];
		// If one of the ancestor pages is excluded, add it to our exclude array
		if ( in_array( $page->ID, $delete_ids ) ) {
			// Finally, delete something(s)
			unset( $pages[$i] );
		}
	}

	// Reindex the array, for neatness
	// SWFIXME: Is reindexing the array going to create a memory optimisation problem for large arrays of WP post/page objects?
	if ( ! is_array( $pages ) ) $pages = (array) $pages;
	$pages = array_values( $pages );

	return $pages;
}

// Recurse down an ancestor chain, checking if one is excluded
// Returns the ID of the "nearest" excluded ancestor
function sdep_ancestor_excluded( & $page, & $excluded_ids, & $pages )
{
	$parent = & sdep_get_page( $page->post_parent, $pages );
	// Is it excluded?
	if ( in_array( $parent->ID, $excluded_ids ) ) {
		return $parent->ID;
	}
	// Is it the homepage?
	if ( $parent->ID == 0 ) return false;
	// Otherwise we have another ancestor to check
	return sdep_ancestor_excluded( $parent, $excluded_ids, $pages );
}

// Return the portion of the $pages array which refers to the ID passed as $page_id
function sdep_get_page( $page_id, & $pages )
{
	// PHP 5 would be much nicer here, we could use foreach by reference, ah well.
	$length = count($pages);
	for ( $i=0; $i<$length; $i++ ) {
		$page = & $pages[$i];
		if ( $page->ID == $page_id ) return $page;
	}
	// Unusual.
	return false;
}

// Is this page we're editing (defined by global $post_ID var) 
// currently NOT excluded (i.e. included),
// returns true if NOT excluded (i.e. included)
// returns false is it IS excluded.
// (Tricky this upside down flag business.)
function sdep_this_page_included()
{
	global $post_ID;
	// New post? Must be included then.
	if ( ! $post_ID ) return true;
	$excluded_ids = sdep_get_excluded_ids();
	// If there's no exclusion array, we can return true
	if ( empty($excluded_ids) ) return true;
	// Check if our page is in the exclusion array
	// The bang (!) reverses the polarity [1] of the boolean
	return ! in_array( $post_ID, $excluded_ids );
	// fn1. (of the neutron flow, ahem)
}

// Check the ancestors for the page we're editing (defined by 
// global $post_ID var), return the ID if the nearest one which
// is excluded (if any);
function sdep_nearest_excluded_ancestor()
{
	global $post_ID, $wpdb;
	// New post? No problem.
	if ( ! $post_ID ) return false;
	$excluded_ids = sdep_get_excluded_ids();
	// Manually get all the pages, to avoid our own filter.
	$sql = "SELECT ID, post_parent FROM $wpdb->posts WHERE post_type = 'page'";
	$pages = $wpdb->get_results( $sql );
	// Start recursively checking the ancestors
	$parent = sdep_get_page( $post_ID, $pages );
	return sdep_ancestor_excluded( $parent, $excluded_ids, $pages );
}

function sdep_get_excluded_ids()
{
	$exclude_ids_str = get_option( sdep_OPTION_NAME );
	// No excluded IDs? Return an empty array
	if ( empty($exclude_ids_str) ) return array();
	// Otherwise, explode the separated string into an array, and return that
	return explode( sdep_OPTION_SEP, $exclude_ids_str );
}

// This function gets all the exclusions out of the options
// table, updates them, and resaves them in the options table.
// We're avoiding making this a postmeta (custom field) because we
// don't want to have to retrieve meta for every page in order to
// determine if it's to be excluded. Storing all the exclusions in
// one row seems more sensible.
function sdep_update_exclusions( $post_ID )
{
	// Bang (!) to reverse the polarity of the boolean, turning include into exclude
	$exclude_this_page = ! (bool) $_POST['sdep_this_page_included'];
	// SWTODO: Also check for a hidden var, which confirms that this checkbox was present
	// If hidden var not present, then default to including the page in the nav (i.e. bomb out here rather
	// than add the page ID to the list of IDs to exclude)
	$ctrl_present = (bool) @ $_POST['sdep_ctrl_present'];
	if ( ! $ctrl_present ) return;
	
	$excluded_ids = sdep_get_excluded_ids();
	// If we need to EXCLUDE the page from the navigation...
	if ( $exclude_this_page ) {
		// Add the post ID to the array of excluded IDs
		array_push( $excluded_ids, $post_ID );
		// De-dupe the array, in case it was there already
		$excluded_ids = array_unique( $excluded_ids );
	}
	// If we need to INCLUDE the page in the navigation...
	if ( ! $exclude_this_page ) {
		// Find the post ID in the array of excluded IDs
		$index = array_search( $post_ID, $excluded_ids );
		// Delete any index found
		if ( $index !== false ) unset( $excluded_ids[$index] );
	}
	$excluded_ids_str = implode( sdep_OPTION_SEP, $excluded_ids );
	sdep_set_option( sdep_OPTION_NAME, $excluded_ids_str, "Comma separated list of post and page IDs to exclude when returning pages from the get_pages function." );
}

// Take an option, delete it if it exists, then add it.
function sdep_set_option( $name, $value, $description )
{
	// Delete option	
	delete_option($name);
	// Insert option
	add_option($name, $value, $description);
}

// Pre WP2.5
// Add some HTML for the DBX sidebar control into the edit page page
function sdep_admin_sidebar()
{
	$nearest_excluded_ancestor = sdep_nearest_excluded_ancestor();
	echo '	<fieldset id="excludepagediv" class="dbx-box">';
	echo '		<h3 class="dbx-handle">'.__('Navigation').'</h3>';
	echo '		<div class="dbx-content">';
	echo '		<label for="sdep_this_page_included" class="selectit">';
	echo '		<input ';
	echo '			type="checkbox" ';
	echo '			name="sdep_this_page_included" ';
	echo '			id="sdep_this_page_included" ';
	if ( sdep_this_page_included() ) echo 'checked="checked"';
	echo ' />';
	echo '			'.__('Include this page in menus').'</label>';
	echo '		<input type="hidden" name="sdep_ctrl_present" value="1" />';
	if ( $nearest_excluded_ancestor !== false ) {
		echo '<div class="exclude_alert">';
		echo __('An ancestor of this page is excluded, so this page is too. ');
		echo '<a href="page.php?action=edit&amp;post='.$nearest_excluded_ancestor.'"';
		echo ' title="'.__('edit the excluded ancestor').'">'.__('Edit ancestor').'</a>.</div>';
	}
	echo '	</div></fieldset>';
}

// Post WP 2.5
// Add some HTML below the submit box
function sdep_admin_sidebar_wp25()
{
	$nearest_excluded_ancestor = sdep_nearest_excluded_ancestor();
	echo '	<div id="excludepagediv" class="new-admin-wp25">';
	echo '		<div class="outer"><div class="inner">';
	echo '		<label for="sdep_this_page_included" class="selectit">';
	echo '		<input ';
	echo '			type="checkbox" ';
	echo '			name="sdep_this_page_included" ';
	echo '			id="sdep_this_page_included" ';
	if ( sdep_this_page_included() ) echo 'checked="checked"';
	echo ' />';
	echo '			'.__('Include this page in user menus').'</label>';
	echo '		<input type="hidden" name="sdep_ctrl_present" value="1" />';
	if ( $nearest_excluded_ancestor !== false ) {
		echo '<div class="exclude_alert"><em>';
		echo __('N.B. An ancestor of this page is excluded, so this page is too. ');
		echo '<a href="page.php?action=edit&amp;post='.$nearest_excluded_ancestor.'"';
		echo ' title="'.__('edit the excluded ancestor').'">'.__('Edit ancestor').'</a>.</em></div>';
	}
	echo '	</div><!-- .inner --></div><!-- .outer -->';
	echo '	</div><!-- #excludepagediv -->';
}

// Add some CSS into the HEAD element of the admin area
function sdep_admin_css()
{
	echo '	<style type="text/css" media="screen">';
	echo '		div.exclude_alert { font-size: 11px; }';
	echo '		.new-admin-wp25 { font-size: 11px; background-color: #fff; }';
	echo '		.new-admin-wp25 div.inner {  padding: 8px 12px; background-color: #EAF3FA; border: 1px solid #EAF3FA; -moz-border-radius: 3px; -khtml-border-bottom-radius: 3px; -webkit-border-bottom-radius: 3px; border-bottom-radius: 3px; }';
	echo '		#sdep_admin_meta_box div.inner {  padding: inherit; background-color: transparent; border: none; }';
	echo '		#sdep_admin_meta_box div.inner label { background-color: none; }';
	echo '		.new-admin-wp25 div.exclude_alert { padding-top: 5px; }';
	echo '		.new-admin-wp25 div.exclude_alert em { font-style: normal; }';
	echo '	</style>';
}

// Add our ctrl to the list of controls which AREN'T hidden
function sdep_hec_show_dbx( $to_show )
{
	array_push( $to_show, 'excludepagediv' );
	return $to_show;
}

// PAUSE & RESUME FUNCTIONS

function sd_pause_exclude_pages()
{
	remove_filter('get_pages','sdep_exclude_pages');
}

function sd_resume_exclude_pages()
{
	add_filter('get_pages','sdep_exclude_pages');
}

// INIT FUNCTIONS

function sdep_init()
{
	// Call this function on the get_pages filter
	// (get_pages filter appears to only be called on the "consumer" side of WP,
	// the admin side must use another function to get the pages. So we're safe to
	// remove these pages every time.)
	add_filter('get_pages','sdep_exclude_pages');
}

function sdep_admin_init()
{
	// Add panels into the editing sidebar(s)
	global $wp_version;
	if (version_compare( $wp_version, '2.7-beta', '>=' ) ) {
		// add_meta_box( $id, $title, $callback, $page, $context, $priority );
		add_meta_box('sdep_admin_meta_box', __('Exclude Pages'), 'sdep_admin_sidebar_wp25', 'page', 'side', 'low');
	} else {
		add_action('dbx_page_sidebar', 'sdep_admin_sidebar'); // Pre WP2.5
		add_action('submitpage_box', 'sdep_admin_sidebar_wp25'); // Post WP 2.5, pre WP 2.7
	}
	
	do_meta_boxes('dropdown-menu-widget','advanced',null);


	// Set the exclusion when the post is saved
	add_action('save_post', 'sdep_update_exclusions');

	// Add some CSS to the admin header
	add_action('admin_head', 'sdep_admin_css');

	// Call this function on our very own hec_show_dbx filter
	// This filter is harmless to add, even if we don't have the 
	// Hide Editor Clutter plugin installed as it's using a custom filter
	// which won't be called except by the HEC plugin.
	// Uncomment to show the control by default
	// add_filter('hec_show_dbx','sdep_hec_show_dbx');
}

// HOOK IT UP TO WORDPRESS
if(!function_exists('sdep_exclude_pages')){
	add_action( 'init', 'sdep_init' );
	add_action( 'admin_init', 'sdep_admin_init' );
};

?>