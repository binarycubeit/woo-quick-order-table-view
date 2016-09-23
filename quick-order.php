<?php
/*
Plugin Name:Woo Quick Order Table View
Description:Products of woocommerce will be showed in table with featured image,title,price,add to cart button.When click on image it will show pop up.use shortcode [woo_qotv_code]
Version 1.1
Author: Abu Rayhan
Author URI: http://binarycubeit.com/
*/


add_action('wp_print_scripts', 'woo_qotv_register_scripts');
add_action('wp_print_styles', 'woo_qotv_register_styles');


function woo_qotv_register_scripts() {
    if (!is_admin()) {
        // register
        wp_register_script('woo_qotv_script', plugins_url('js/featherlight.min.js', __FILE__));
 
        // enqueue
        wp_enqueue_script('woo_qotv_script');
    }
}
 
function woo_qotv_register_styles() {
    // register
    wp_register_style('woo_qotv_style', plugins_url('css/featherlight.min.css', __FILE__));
    wp_register_style('woo_qotv_style_main', plugins_url('css/style.css', __FILE__));
 
    // enqueue
    wp_enqueue_style('woo_qotv_style');
    wp_enqueue_style('woo_qotv_style_main');
}


register_activation_hook(__FILE__, 'woo_qotv_activation');
register_deactivation_hook(__FILE__, 'woo_qotv_deactivation');

function woo_qotv_activation() {
    
    //actions to perform once on plugin activation go here    
    	
    //register uninstaller
    register_uninstall_hook(__FILE__, 'woo_qotv_uninstall');
}

function woo_qotv_uninstall(){
    
    //actions to perform once on plugin uninstall go here	    
}



function woo_qotv(){?>


<table class="quick-order">
<tr class="top_part">
<thead>
<th ><?php echo('Image'); ?></th>
<th ><?php echo('Product Name'); ?></th>
<th ><?php echo('Price'); ?></th>
<th ><?php echo('Quantity'); ?></th>
</thead>
</tr>

<?php  $args = array( 'post_type' => 'product', 'orderby' =>'date','order' => 'DESC' );
        $loop = new WP_Query( $args );
        while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
<tr>
<?php if ( $product->is_in_stock() ) : ?>
<td><a class="btn btn-default product" href="#" data-featherlight="#product_details_<?php echo $loop->post->ID; ?>"><div class="normal_thumnail"><?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'"/>'; ?></div></a></td>
<td><?php the_title(); ?></td>
<td><?php echo woocommerce_price($product->get_price()); ?></td>
<td>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart_2" method="post" enctype='multipart/form-data'>
	 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	 	<?php
	 		if ( ! $product->is_sold_individually() ) {
	 			woocommerce_quantity_input( array(
	 				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
	 				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
	 				
	 			) );
	 		}
	 	?><input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />

	 <button type="submit" class="single_add_to_cart_button button alt">+</button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>
</td>


<div class="lightbox" id="product_details_<?php echo $loop->post->ID; ?>" >

<div class="light_image"><?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'"/>'; ?></div>
<div class="light_details">
<h2><?php the_title(); ?></h2>
<div class="description"><?php the_content(); ?></div>
<p>SKU: <?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce' ); ?></p>
<p>Price: <?php echo woocommerce_price($product->get_price()); ?></p>
</div
</div>
<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
<?php endif; ?>
</tr>


<?php endwhile; ?>
</table>

<?php

echo "Pagination <hr>";

previous_posts_link( '« Prev' );

next_posts_link("Next »", $loop->max_num_pages);

wp_reset_query();

}

add_shortcode('woo_qotv_code','woo_qotv');

?>