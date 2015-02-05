<?php
$cg_shortcodes = array(
	'cg_the_title' => array(
		'arg' => array(),
		'callback' => function($atts){
			return get_the_title();
		}
	),
	'cg_the_category' => array(
		'arg' => array(),
		'callback' => function($atts){
			$categories = get_the_category();
			return is_object($categories)?implode(',',$categories):'';
		}
	),
	'cg_the_author' => array(
		'arg' => array(),
		'callback' => function($atts){
			return get_the_author();
		}
	),
	'cg_the_content' => array(
		'arg' => array(),
		'callback' => function($atts){
			return do_shortcode(get_the_content());
		}
	),
	'cg_the_excerpt' => array(
		'arg' => array(),
		'callback' => function($atts){
			return get_the_excerpt();
		}
	)
);
foreach($cg_shortcodes as $s_name=>$array){
	add_shortcode($s_name,$array['callback']);
}