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

  /*  change the out of stock notifications for early bird & pj sale
  */
  add_filter('woocommerce_get_availability_text', 'reach_get_avail_text', 10, 2);
  function reach_get_avail_text($text, $product) {
      $id = $product->get_id();

      if (!$product->is_in_stock()) {
          if ($id == 1505) {
            $text = 'Available 6am-8am<br> Nov 11th'; // islander pj sale
          }
          if ($id == 1483) {
            $text = 'Available 6am-8am <br> Nov 4th'; // american early bird sale
          }
          if ($id == 3599) {
            $text = 'Available on Jan 20th'; // 20 on 20th.
          }
          if ($id == 645) {
            $text = 'Deadline for submission has past.'; // 20 on 20th.
          }
      }
      return $text;
  }


  add_action( 'woocommerce_before_checkout_form', 'eai_checkout_message', 10 );
  add_action( 'woocommerce_after_checkout_form', 'eai_checkout_message', 10 );

  function eai_checkout_message( ) {
   echo '<div class="eai-checkout-message"><h3>If you have trouble placing your order, please send an email to <a target="_blank"  href="mailto:kwescott@ellsworthamerican.com">kwescott@ellsworthamerican.com</a> or call <a href="tel:2072880556">(207) 667-2576</a>.</h3></div>';
  }
