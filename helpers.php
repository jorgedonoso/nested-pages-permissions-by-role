<?php

/*
* Returns the absolute parent id for a post id.
*/
function _helpers_wp_get_abs_parent_id($post_id){
            
    while($post_id != 0){
        $parent_id = $post_id;
        $post_id = wp_get_post_parent_id($post_id);
    }

    return $parent_id;
}

/*
* Updates the parent meta for a specific post.
*/
function update_parent_meta($id){
    
    $parent_id = wp_get_post_parent_id($id);

    if($parent_id==0){
        $parent_id = $id;
    }

    $the_parent_title = get_the_title($parent_id);
    
    update_post_meta($id, '_nppbr_parent_page', $the_parent_title);
}

/*
* Updates the absolute parent meta for a specific post.
*/
function update_absolute_parent_meta($id){

    $absolute_parent_id = _helpers_wp_get_abs_parent_id($id);
    $the_absolute_title = get_the_title($absolute_parent_id);

    update_post_meta($id, '_nppbr_absolute_parent_page', $the_absolute_title);
}

?>