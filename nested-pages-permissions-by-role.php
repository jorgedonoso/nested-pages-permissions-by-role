<?php
/*
Plugin Name: Nested Pages Permissions by Role
Description: This plugin restricts users to only be able to edit pages under their assigned page tree.
Version: 0.2.25
Author: Jorge Donoso
*/

/*
* Add the column header for the "Parent Page".
*/
add_filter('manage_pages_columns', 'nested_pages_permissions_by_role_column_header', 5);

function nested_pages_permissions_by_role_column_header($defaults){
    $defaults['nested_pages_permissions_by_role_parent_page'] = __('Direct Parent Page');
    return $defaults;
}

/*
* Populate the content of the "Parent Page" for each page.
*/
add_action('manage_pages_custom_column', 'nested_pages_permissions_by_role_column_content', 5, 2);

function nested_pages_permissions_by_role_column_content($column_name, $id){
    if($column_name === 'nested_pages_permissions_by_role_parent_page'){
        $parent_id = wp_get_post_parent_id($id);
        if($parent_id!=0){
            $the_title = get_the_title($parent_id);
            echo $the_title;
        }
    }
}

?>