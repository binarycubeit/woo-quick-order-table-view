
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



function woo_qotv($atts){

extract(shortcode_atts(array(
        'products_per_page'   => -1,
        'category'      => '',
        'orderby'       => 'meta_value_num',
        'meta_key'      => '_price',
        'order'         => 'asc',
        'header_color'   => '#ccc',
        'table_color'   => '#efefef'
        ), $atts));



    ?>

   <?php  wc_print_notices();?>

    <?php $return_string =' 
        <table class="quick-order" style="background:'.$table_color.';">
        <tr class="top_part">
        <thead style="background:'.$header_color.';">
        <th>Image</th>
        <th>Product Name</th>
        <th>Price</th>
        <th>Quantity</th>
        </thead>
        </tr>';
    
    
    $args = array( 
        'post_type' => 'product',
        'product_cat'=>$category,
        'posts_per_page' =>$products_per_page,
        'orderby'   => 'meta_value_num',
        'meta_key'  => '_price',
        'order' => $order); 
         $loop = new WP_Query( $args );
         while ( $loop->have_posts() ) : $loop->the_post();global $product; 
         if ( $product->is_in_stock() ) : ?>
         <?php if ( $product->is_type( 'simple' ) ){?>


        <?php $return_string .='<tr>';?>
        <?php $return_string .='<td><a class="btn btn-default product" href="#" data-featherlight="#product_details_'.$loop->post->ID.'"><div class="normal_thumnail"><img src="'.get_the_post_thumbnail_url( $loop->post->ID).'"></div></a></td>';?>
        <?php $return_string .='<td>'.get_the_title().'</td>';?>
        <?php $return_string .='<td>'.woocommerce_price($product->get_price()).'</td>';?> 
        
        <?php $return_string .='<td><form class="cart" method="post" enctype="multipart/form-data">
                                <div class="quantity">
                                    <input type="number" step="1" min="1" max="" name="quantity" value="1" title="Quantity" class="input-text qty text" size="4" pattern="[0-9]*" inputmode="numeric">
                                </div>
                                <input type="hidden" name="add-to-cart" value="'.get_the_ID().'">
                                    <button type="submit" class="single_add_to_cart_button button alt"><i class="fa fa-cart-plus" aria-hidden="true"></i> +</button>
                                </form></td>';?>

        
        <?php $return_string .='<div class="lightbox" id="product_details_'.$loop->post->ID.'">'; ?>
        <?php $return_string .='<div class="light_image"><img src="'.get_the_post_thumbnail_url( $loop->post->ID).'"></div>';?>
        <?php $return_string .='<div class="light_details">';?>
        <?php $return_string .='<h2>'.get_the_title().'</h2>';?>
        <?php $return_string .='<div class="description">'.get_the_content().'</div>';?>
        <?php $return_string .='<p>SKU:'.($sku = $product->get_sku() ).'</p>';?>
        <?php $return_string .='<p>Price:'.woocommerce_price($product->get_price()).'</p>';?>
        <?php $return_string .='</div>';?>
        <?php $return_string .='</div>';?>
        <?php $return_string .='</tr>'; ?>

        <?php } endif; ?>
        <?php endwhile; ?>
        <?php $return_string .='</table>';?> 
        <?php return $return_string; ?>

        <?php  }

        add_shortcode('woo_qotv_code','woo_qotv');

        ?>

