<?php
/* languages customizations
*/
	if ( !function_exists('reach_change_theme_text') ){
		function reach_change_theme_text( $translated_text, $text, $domain ) {
			 /* if ( is_singular() ) { */
			    switch ($domain) {

						case 'gravityview':
							switch ( $translated_text ) {
					            case 'No entries match your request.' :
					                $translated_text = 'There are no cancellations.';
					                break;
					           /*case 'Add to cart':
					            	$translated_text = __( 'Continue to Checkout',  'woocommerce'  );
					            	break;*/
					        }
							break;
					default:
					 /* switch ( $translated_text ) {
				          case 'Under :' :
				                $translated_text = __( '',  $domain  );
				                break;
				         	case 'Type here...':
				            	$translated_text = __( 'Search...',  $domain  );
				            	break;
				            case 'BLOG CATEGORIES':
				            	$translated_text = __( 'Found in',  $domain  );
				            	break;
				            case 'Share this post:':
				            	$translated_text = __('Share', ' $domain );
				            	break;
				        } */

				}

	    	return $translated_text;
		} // end function reach_change_theme_text
		add_filter( 'gettext', 'reach_change_theme_text', 20, 3 );
	} // end if not exists reach_change_theme_text
?>
