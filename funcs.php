<?php 
function cb_get_terms_with_taxonomies($post_type){
	$taxonomies = get_object_taxonomies($post_type,'names');
	
	$args = array(
		'orderby'           => 'name', 
		'order'             => 'ASC',
		'hide_empty'        => true, 
		'exclude'           => array(), 
		'exclude_tree'      => array(), 
		'include'           => array(),
		'number'            => '', 
		'fields'            => 'all', 
		'slug'              => '',
		'parent'            => '',
		'hierarchical'      => true, 
		'child_of'          => 0, 
		'get'               => '', 
		'name__like'        => '',
		'description__like' => '',
		'pad_counts'        => false, 
		'offset'            => '', 
		'search'            => '', 
		'cache_domain'      => 'core'
	); 
	
	$terms = get_terms($taxonomies, $args);
	$term_tax_array = array();
	foreach($terms as $each){
		$term_tax_array[$each->taxonomy][] = array('id'=>$each->term_id,'name'=>$each->name);
	}
	return $term_tax_array;
	
	
}

function cg_set_term_taxonomies($term_tax_array,$cat_id,$obj_field_name,$obj_field_id){
	foreach($term_tax_array as $tax_name=>$array){
		?>
		<h4><?php echo $tax_name?></h4>
		<?php 
		
		foreach($array as $each_term){
			?>
			<input type="checkbox" name="<?php echo $obj_field_name; ?>[]" id="<?php echo $obj_field_id; ?>[]" value="<?php echo $each_term['id']; ?>" <?php echo (is_array($cat_id) && in_array($each_term['id'],$cat_id))?'checked':''; ?> /> <?php echo $each_term['name']; ?>
			<?php
		}
		
	}
}