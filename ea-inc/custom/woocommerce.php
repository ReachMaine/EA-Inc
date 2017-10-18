<?php

// to remove sku from everywhere....
add_filter( 'wc_product_sku_enabled', '__return_false' );

add_filter ('woocommerce_product_description_tab_title','eainc_remove_descr_tab_title' );
function eainc_remove_descr_tab_title($title) {
    return '';
}

// change the placeholder text in the order comments.
add_filter('woocommerce_checkout_fields', 'custom_woocommerce_checkout_fields');
function custom_woocommerce_checkout_fields( $fields ) {

     $fields['order']['order_comments']['placeholder'] = 'Use this field for gift subscription email address.';

     return $fields;
}

//
add_action( 'woocommerce_email_before_order_table', 'add_order_email_instructions', 10, 2 );
function add_order_email_instructions( $order, $sent_to_admin ) {
  if ( ! $sent_to_admin ) {
       echo '<p>If your order includes a digital subscription, you will receive a separate email with your password by the 17th.</p>';
  }
}
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

	function woo_remove_product_tabs( $tabs ) {
		unset( $tabs['additional_information'] );  	// Remove the additional information tab

		return $tabs;

	}
