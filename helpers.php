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

?>