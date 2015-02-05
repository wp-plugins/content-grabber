<?php 
include 'funcs.php';
add_action('wp_ajax_get_terms_taxonomies','get_terms_taxonomies');
function get_terms_taxonomies(){
	$term_tax_array = cb_get_terms_with_taxonomies($_POST['post_type']);
	cg_set_term_taxonomies($term_tax_array,json_decode(stripslashes($_POST['cat_id_array'])),$_POST['obj_field_name'],$_POST['obj_field_id']);
	exit;
}
