<?php
/*
Plugin Name: Nested Pages Permissions by Role
Description: This plugin restricts users from editing pages outside their assigned page tree.
Version: 0.2.25
Author: Jorge Donoso
*/

namespace nppbr_space;

require('helpers.php');

/*
* Add column headers for direct and absolute parents.
*/
add_filter('manage_pages_columns', 'nppbr_space\nested_pages_permissions_by_role_column_header', 5);

function nested_pages_permissions_by_role_column_header($defaults){
    $defaults['nested_pages_permissions_by_role_parent_page'] = __('Direct Parent');
    $defaults['nested_pages_permissions_by_role_absolute_parent_page'] = __('Absolute Parent');
    return $defaults;
}

/*
* Populate content cells.
*/
add_action('manage_pages_custom_column', 'nppbr_space\nested_pages_permissions_by_role_column_content', 5, 2);

function nested_pages_permissions_by_role_column_content($column_name, $id){
	
	/*
	* Populate direct parent content.
	*/
    if($column_name === 'nested_pages_permissions_by_role_parent_page'){
        echo get_post_meta($id, '_nppbr_parent_page', true );
    }

    /*
    * Populate absolute parent content.
    */
    if($column_name === 'nested_pages_permissions_by_role_absolute_parent_page'){
       	echo get_post_meta($id, '_nppbr_absolute_parent_page', true );
    }
}

/*
* Make columns sortable
*/
add_filter('manage_edit-page_sortable_columns','nppbr_space\nested_pages_permissions_by_role_sortable_columns');

function nested_pages_permissions_by_role_sortable_columns($columns){
	$columns['nested_pages_permissions_by_role_parent_page'] = 'parent_page';
	$columns['nested_pages_permissions_by_role_absolute_parent_page'] = 'absolute_parent_page';
	return $columns;
}

add_action('pre_get_posts', 'nppbr_space\nppbr_orderby');
function nppbr_orderby($query) {

	if($query->get("post_type")=='page'){

		$orderby = $query->get('orderby');
		
		if($orderby == 'parent_page'){
            $query->set('meta_key','_nppbr_parent_page');
            $query->set('orderby','meta_value');
		}
		
		if($orderby == 'absolute_parent_page'){
            $query->set('meta_key','_nppbr_absolute_parent_page');
            $query->set('orderby','meta_value');
		}
	}

	return $query;
}

/*
* Activation hook.
*/
register_activation_hook( __FILE__, 'nppbr_space\nppbr_activation' );
function nppbr_activation() {
	
	$pages = get_pages(); 
	foreach ( $pages as $page ) {
		update_parent_meta($page->ID);
		update_absolute_parent_meta($page->ID);
 	}

}

/*
* Deactivation hook.
*/
register_deactivation_hook( __FILE__, 'nppbr_space\nppbr_deactivation' );

function nppbr_deactivation(){
	$pages = get_pages(); 
	foreach ( $pages as $page ) {
		delete_post_meta($page->ID,'_nppbr_parent_page');
		delete_post_meta($page->ID,'_nppbr_absolute_parent_page');
 	}
}

/*
* Update the meta on create or update.
*/
add_action('save_post_page', 'nppbr_space\nppbr_save_post_page' );

function nppbr_save_post_page($id){
	update_parent_meta($id);
	update_absolute_parent_meta($id);
}

/*
* Settings page to manage roles' relationships.
*/
add_action('admin_menu', 'nppbr_space\nppbr_settings' );

function nppbr_settings() {
	add_options_page(
		'Nested Pages Permissions',
		'Nested Pages Permissions',
		'manage_options',
		'Nested Pages Permissions',
		'nppbr_space\nppbr_settings_page');
}

function nppbr_settings_page() {
	
	global $wp_roles;
    $all_roles = $wp_roles->roles;
    
	?> 
		<h1>Nested Pages Permissions by Role</h1>
		<h2>Settings</h2>

		<form method="post" action="options.php">

            <?php 
            	wp_nonce_field('update-options'); 
	            $roles_array = [];

	            foreach($all_roles as $role){
	    			print "<p><strong>".$role['name']."</strong></p>";
	    			nppbr_roles_dropdown($role['name']);
	    			array_push($roles_array, 'nppbr_'.sanitize_title_with_dashes($role['name']));
	    		}

	    		$roles_for_hidden_input = implode(",",$roles_array);
    		?>

            <p><input type="submit" name="Submit" value="Save" /></p>
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="<?php print $roles_for_hidden_input ?>" />
        </form>

	<?php

}

function nppbr_roles_dropdown($role)
{

	$page_ids=get_all_page_ids();
	$sanitized_role = sanitize_title_with_dashes($role);

	print "<select name='nppbr_$sanitized_role'><option>-</option>";
	foreach($page_ids as $page)
	{
		print "<option value='$page' ".selected(get_option("nppbr_$sanitized_role"), $page).'>'.get_the_title($page).'</option>';
	}
	echo '</select>';

}
?>