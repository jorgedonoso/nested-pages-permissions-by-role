<?php
/*
Plugin Name: Nested Pages Permissions by Role
Description: This plugin restricts users to only be able to edit pages under their assigned page tree.
Version: 0.2.25
Author: Jorge Donoso
*/

require('helpers.php');

/*
* Add column headers for direct and absolute parents.
*/
add_filter('manage_pages_columns', 'nested_pages_permissions_by_role_column_header', 5);

function nested_pages_permissions_by_role_column_header($defaults){
    $defaults['nested_pages_permissions_by_role_parent_page'] = __('Direct Parent');
    $defaults['nested_pages_permissions_by_role_absolute_parent_page'] = __('Absolute Parent');
    return $defaults;
}

/*
* Populate content cells.
*/
add_action('manage_pages_custom_column', 'nested_pages_permissions_by_role_column_content', 5, 2);

function nested_pages_permissions_by_role_column_content($column_name, $id){
	
	/*
	* Populate direct parent content.
	*/
    if($column_name === 'nested_pages_permissions_by_role_parent_page'){
        $parent_id = wp_get_post_parent_id($id);
        
        if($parent_id!=0){
            $the_title = get_the_title($parent_id);
        }

        echo $the_title;
    }

    /*
    * Populate absolute parent content.
    */
    if($column_name === 'nested_pages_permissions_by_role_absolute_parent_page'){
       	$absolute_parent_id = _helpers_wp_get_abs_parent_id($id);

       	if($id != $absolute_parent_id){
        	$the_title = get_the_title($absolute_parent_id);
    	}

        echo $the_title;
    }
}

?>