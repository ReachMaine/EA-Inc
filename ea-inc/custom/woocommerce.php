<?php

// to remove sku from everywhere....
add_filter( 'wc_product_sku_enabled', '__return_false' );

add_filter ('woocommerce_product_description_tab_title','eainc_remove_descr_tab_title' );
function eainc_remove_descr_tab_title($title) {
    return '';
}
