<?php
/*
Plugin Name: Content Grabber
Plugin URI: 
Description: A widget to help user to grab post of any post type how he wants.
Author: Mithu A Quayium, CyberCraft
Author URI: http://www.cybercraftit.com/
Text Domain: cg
Version: 1.0
*/
// Block direct requests
include_once 'shortcode.php';
include_once 'ajax.php';
if ( !defined('ABSPATH') )
	die('-1');
	
	
add_action( 'widgets_init', function(){
     register_widget( 'cg_content_grabber' );
});	

/**
 * Adds My_Widget widget.
 */
 $cg_posts = '';
class cg_content_grabber extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'cg_content_grabber', // Base ID
			__('Content Grabber', 'cg'), // Name
			array('description' => __( 'Grab post of any type you want with custom settings', 'cg' ),) // Args
		);
		add_action('admin_enqueue_scripts',array($this,'enqueue_scripts_styles'));
	}

	/**
	 * Front-end display of widget.
	 *
	 */
	public function widget( $args, $i ) {
		global $cg_posts;
		if ( array_key_exists('before_widget', $args) ) echo $args['before_widget'];
		?>
			<style>
				<?php 
				echo $i['post_loop_css'];
				?>
			</style>
		<?php
		
		$args = array(
			'post_type' => ( $i['post_type']?$i['post_type']:'post' ),
			'posts_per_page' => ( ( $i['post_per_page'] && is_numeric($i['post_per_page']) )?$i['post_per_page']:5 ),
			'order_by' => (($i['order_by'])?$i['order_by'] : 'category' ),
			'order' => ( $i['order']?$i['order']:'DESC' ),
			'post_status'      => ( $i['post_status']?$i['post_status']:'published' ),
			'category__in' => is_array($i['cat_id'])?$i['cat_id']:'',
		);
		$cg_posts = new WP_Query( $args );
		
		if ( array_key_exists('before_widget', $args) ) echo $args['before_title'];
			
			echo $i['title'];
			
		if ( array_key_exists('after_widget', $args) ) echo $args['after_title'];
		
		
		if($cg_posts->have_posts()):
			?>
			<div class="cg_wrapper">
			<?php
			while($cg_posts->have_posts()):
				$cg_posts->the_post();
				echo do_shortcode($i['post_loop_html']);
			endwhile;
			wp_reset_query();
			?>
            <div class="cg_posts_nav">
            	<div class="cg_next_posts_nav"><?php next_posts_link();?></div>
               <div class="cg_prev_posts_nav"><?php previous_posts_link();?></div>
            </div>			
			<?php
			$cg_posts = '';
			?>
			</div>
			<?php
		else:
			echo __( 'No recent post found.', 'text_domain' );
		endif;
		
		if ( array_key_exists('after_widget', $args) ) echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $i ) {
		
		extract($i);
		
		$post_types = get_post_types();
		?>
        <div class="cg_table_holder">
            <table vspace="10">
                <?php //post type ?>
                <tr>
                	<td>
                      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
                    </td>
                    <td>
                        <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo isset($i['title'])?$i['title']:''; ?>" />   
                    </td>
                </tr>
                <tr>
                    <td>
                      <label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type:' ); ?></label>
                    </td>
                    <td>
                       <select class="cg_select_post_type" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
                            <?php 
                            foreach($post_types as $each_post_type){
                                ?>
                                <option value="<?php echo $each_post_type;?>" <?php echo isset($post_type)?($post_type == $each_post_type?'selected':''):''; ?>> <?php echo $each_post_type; ?> </option>
                                <?php
                            }
                            ?>
                        </select>     
                    </td>
                </tr>
                <?php
                //post per page
                ?>
                <tr>
                    <td>
                        <label for="<?php echo $this->get_field_id( 'post_per_page' ); ?>"><?php _e( 'Number of post(s):' ); ?></label>
                    </td>
                    <td>
                        <input type="text" id="<?php echo $this->get_field_id( 'post_per_page' ); ?>" name="<?php echo $this->get_field_name( 'post_per_page' ); ?>" value="<?php echo isset($i['post_per_page'])?$i['post_per_page']:''; ?>" />
                    </td>
                </tr>
                <?php //order by ?>
                <tr>
                    <td>
                        <label for="<?php echo $this->get_field_id( 'order_by' ); ?>"><?php _e( 'Order by :' ); ?></label>
                    </td>
                    <td>
                         <input type="text" id="<?php echo $this->get_field_id( 'order_by' ); ?>" name="<?php echo $this->get_field_name( 'order_by' ); ?>" value="<?php echo isset($i['order_by'])?$i['order_by']:''; ?>" />
                    </td>
                </tr>
                <?php //order ?>
                <tr>
                    <td>
                        <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order :' ); ?></label>
                    </td>
                    <td>
                        <select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
                            <option value="asc" <?php echo isset($order)?($order == 'asc'?'selected':''):''; ?>>Ascending</option>
                            <option value="desc" <?php echo isset($order)?($order == 'desc'?'selected':''):''; ?>>Descending</option>
                        </select>
                    </td>
                </tr>
                <?php //post status ?>
                <tr>
                    <td>
                        <label for="<?php echo $this->get_field_id( 'post_status' ); ?>"><?php _e( 'Post status :' ); ?></label>
                    </td>
                    <td>
                        <select id="<?php echo $this->get_field_id( 'post_status' ); ?>" name="<?php echo $this->get_field_name( 'post_status' ); ?>">
                            <?php $post_statuses = get_post_statuses();
                            foreach($post_statuses as $post_status => $label){
                                ?>
                                <option value="<?php echo $post_status; ?>" <?php if(isset($i['post_status']) && $i['post_status']==$post_status ) echo 'selected'; ?> > <?php echo $label; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                	<td colspan="2">
					 <?php 
                        $term_tax_array = cb_get_terms_with_taxonomies($i['post_type']?$i['post_type']:'post'); 
                        ?>
                        <div class="cg_term_taxonomies">
                            <?php cg_set_term_taxonomies($term_tax_array,$cat_id,$this->get_field_name( 'cat_id' ),$this->get_field_id( 'cat_id' )); ?>
                        </div>
                        <script>
                        (function($){
                            var obj_field_name = '<?php echo $this->get_field_name( 'cat_id' ); ?>';
                            var obj_field_id = '<?php echo $this->get_field_id( 'cat_id' ); ?>';
                            var $cat_id = '<?php echo json_encode($cat_id);?>';
                            $(document).on('change','.cg_select_post_type',function(){
                            console.log($cat_id);
                                var post_type = $(this).val();
                                $.post(
                                    ajaxurl,
                                    {
                                        'action':'get_terms_taxonomies',
                                        'post_type' : post_type,
                                        'obj_field_name':obj_field_name,
                                        'obj_field_id' : obj_field_id,
                                        'cat_id_array' : JSON.stringify(JSON.parse($cat_id))
                                    },
                                    function(data){
                                        $('.cg_term_taxonomies').html(data);
                                    }
                                    
                                )
                            })
                        }(jQuery))
                        </script>
                    </td>
                </tr>
                <?php //how to show ?>
                <tr>
                    <td colspan="2">
	                <label for="<?php echo $this->get_field_id( 'post_loop_html' ); ?>"><?php _e( '<h4>How you want the posts to appear in front end :</h4>' ); ?></label>
                    <strong>Shortcodes:</strong>
                    <p>[cg_the_title] = Use this to show title</p>
                    <p>[cg_the_content] = Use this to show content</p>
                    <p>[cg_the_category] = Use this to show category</p>
                    <p>[cg_the_excerpt] = Use this to show excerpt</p>
                    <p>[cg_the_author] = Use this to show author</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <h4>HTML</h4>
                    <textarea rows="10" style="width:100%" id="<?php echo $this->get_field_id( 'post_loop_html' ); ?>" name="<?php echo $this->get_field_name( 'post_loop_html' ); ?>">
                        <?php 
                        if(isset($i['post_loop_html'])){
                            echo trim($i['post_loop_html']);
                        }else{
                        	$default_textarea_value = '<div class="title">[cg_the_title]</div><div>[cg_the_category]</div><div>[cg_the_author]</div><div class="content">[cg_the_content]</div>';
							echo trim($default_textarea_value);
                        }
                        ?>
                    </textarea>
                    </td>
                </tr>
                <?php //CSS ?>
                <tr>
                    <td colspan="2">
                    <label for="<?php echo $this->get_field_id( 'post_loop_css' ); ?>"><?php _e( 'Post Loop CSS :' ); ?></label>
                    <textarea rows="10" style="width:100%" id="<?php echo $this->get_field_id( 'post_loop_css' ); ?>" name="<?php echo $this->get_field_name( 'post_loop_css' ); ?>"><?php 
                        if(isset($i['post_loop_css'])){
                            echo trim($i['post_loop_css']);
                        }else{ ?>.title{
  color:#000;
}
 .content{
font-size:14px;
}    
                        <?php
                        
                        }
                        ?>
                    </textarea>
                    </td>
                </tr>
            </table>
        </div>
		<?php
	}

	public function update( $n_i, $o_i) {
		
		$i = array();
		$i['title'] = ( ! empty( $n_i['title'] ) ) ? strip_tags( $n_i['title'] ) : '';
		$i['post_type'] = ( ! empty( $n_i['post_type'] ) ) ? strip_tags( $n_i['post_type'] ) : 'post';
		$i['post_per_page'] = ( ! empty( $n_i['post_per_page'] ) ) ? strip_tags( $n_i['post_per_page'] ) : '-1';
		$i['order_by'] = ( ! empty( $n_i['order_by'] ) ) ? strip_tags( $n_i['order_by'] ) : 'category';
		$i['order'] = ( ! empty( $n_i['order'] ) ) ? strip_tags( $n_i['order'] ) : 'desc';
		$i['post_status'] = ( ! empty( $n_i['post_status'] ) ) ? strip_tags( $n_i['post_status'] ) : 'published';
		$i['post_loop_html'] = $n_i['post_loop_html'];
		$i['post_loop_css'] = strip_tags($n_i['post_loop_css']);
		$i['cat_id'] = $n_i['cat_id'];
		return $i;
	}
	public function enqueue_scripts_styles(){
		wp_enqueue_style('cg_style',plugins_url('css/style.css',__FILE__));
	}
} 