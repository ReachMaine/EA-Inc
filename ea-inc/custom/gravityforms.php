<?php
/* custom programing for gravity forms */
// validated email for cancellation submissions form
add_filter( 'gform_validation_6', 'eai_validate_useremail' );
function eai_validate_useremail ( $validation_result) {
    // take from example at https://docs.gravityforms.com/using-the-gravity-forms-gform-validation-hook/
    /* validate_email */
    // 2 - Get the form object from the validation result
    $form = $validation_result['form'];
    //echo "<pre>"; var_dump($form); echo "</pre>";
    // 3 - Get the current page being validated
    $current_page = rgpost( 'gform_source_page_number_' . $form['id'] ) ? rgpost( 'gform_source_page_number_' . $form['id'] ) : 1;

    // 4 - Loop through the form fields
    foreach( $form['fields'] as &$field ) {
      // 5 - If the field does not have our designated CSS class, skip it
      if ( strpos( $field->cssClass, 'validate_email' ) === false ) {
          continue;
      } else {
        //echo "<pre>validate_email class.  field : ".$field['id']." </pre>";
      }
      // 6 - Get the field's page number
      $field_page = $field->pageNumber;

      // 7 - Check if the field is hidden by GF conditional logic
      $is_hidden = RGFormsModel::is_field_hidden( $form, $field, array() );

      // 8 - If the field is not on the current page OR if the field is hidden, skip it
      if ( $field_page != $current_page || $is_hidden ) {
          continue;
      }
      // 9 - Get the submitted value from the $_POST
      $field_value = rgpost( "input_{$field['id']}" );

      //** validate here ***/
      $user_id_exists =  email_exists( $field_value );
      //echo "<pre> field : ".$field['id']." value is: ".$field_value."</pre>";
      $is_okay = true;
      if ($user_id_exists) {
          // test that user is valid on this blog (in multi-site)
          if (!is_user_member_of_blog($user_id_exists)) {
             $is_okay= false;
          }
      } else {
        $is_okay = false;
      }
      if ($is_okay) {
        continue;
      } else {
        // failed,
        // 12 - The field field validation, so first we'll need to fail the validation for the entire form
        $validation_result['is_valid'] = false;

        // 13 - Next we'll mark the specific field that failed and add a custom validation message
        $field->failed_validation = true;
        $field->validation_message = 'Please register your email address to be authorized to submit cancellations.';

        // 14 - Assign our modified $form object back to the validation result
        $validation_result['form'] = $form;
      } // end not okay.
    } // end for loop
    return $validation_result;
}

//   add email for registration form
add_action( 'gform_after_submission_5', 'eai_add_email', 10, 2 );
function eai_add_email($entry, $form) {
  $dbg_out = "";
  // get the email from the entry (field number 2)
  $new_email = rgar( $entry, '2' );
  $userid = email_exists( $new_email );

  if (!$userid) {
      $dbg_out .= "new email: ".$new_email;
     // new user  -- try first name last name combo as username
     $first_name = rgar($entry, '1.3');
     $last_name = rgar($entry, '1.6');
     $username =  $first_name.$last_name.rand(0,9999); // firstname+lastname
     $dbg_out .= "submitted name: $username";
     $userdata = array(
        'user_login'  =>  $username,
        'user_email' => $new_email,
        'user_pass'   =>  wp_generate_password(),
        'first_name' => rgar($entry, '1.3'),
        'last_name' => rgar($entry, '1.6'),
        'display_name' => rgar($entry, '4'),
        'nickname' => rgar($entry, '4'),
        'role' => ''
     );
     $userid = wp_insert_user($userdata);
     //$userid = wp_create_user( $submitted_name, $random_password, $new_email, "" );
     //On success
      if ( ! is_wp_error( $userid ) ) {
       $dbg_out .= "created user with id: {$userid} ";
     } else {
       $dbg_out .= "failed to create user.";
       echo "<pre>"; var_dump($userid); echo "</pre>";
     }
  }
  if ($userid) { // got here with a user (new or existing)
      $dbg_out .= "yep: ".$new_email;
      if (is_user_member_of_blog($userid)) {
        $dbg_out .= " member. - done";
      } else {
        $dbg_out .= " not a member ";
        // add user to this blog...
        $blog_id = get_current_blog_id();
        if (add_user_to_blog( $blog_id, $userid ) ) { // add user with no role.
          // sucess
          $dbg_out .= "added user to blog.";
        } else {
          $dbg_out .= " couldnt add user, id: { ".$userid. " } ";
        }
      }
  } else {
    $dbg_out .= "ERROR! ";
  }
  /* for debugging
  // getting post
  $post = get_post( $entry['post_id'] );
  // changing post content
  $post->post_content .= $dbg_out."<br>" ;
  // updating post
  wp_update_post( $post );*/
}



// add the shortcode to search for today.
if (!function_exists('eai_gravityview_today')) {
	function eai_gravityview_today( $atts ) {
		$atts = shortcode_atts( array(
			'id' => '3015',
			'searchfield' => '2',
		), $atts, 'gravityview_today' );
    $today_dt = new DateTime('America/New_York');
    $today_str = $today_dt->format("Y-m-d");
    $today_display = $today_dt->format("l, F j, Y"); // friday, December 21, 2017
    $outstr = "";
    $outstr .= "<h3> Cancellations for ".$today_display."</h3>";
    $shortcode_str = '[gravityview id="'.$atts['id'].'" search_field="'.$atts['searchfield'].'" search_value="'.$today_str.'"]' ;
    //$outstr .= $today_str;
    $outstr .=  do_shortcode($shortcode_str);
		return "{$outstr}";
	}
}
add_shortcode( 'gravityview_today', 'eai_gravityview_today' );

if (!function_exists('eai_gravityview_tomorrow')) {
	function eai_gravityview_tomorrow( $atts ) {
		$atts = shortcode_atts( array(
			'id' => '3015',
			'searchfield' => '2',
		), $atts, 'gravityview_tomorrow' );
    $tomorrow =  new DateTime('America/New_York');
    $tomorrow -> add(new DateInterval('P1D')); // add one day
    $tomorrow_str = $tomorrow->format("Y-m-d");
    $tomorrow_str_display = $tomorrow->format("l, F j, Y");
    $outstr = "";
    $outstr .= "<h3> Cancellations for ".$tomorrow_str_display."</h3>";
    $shortcode_str = '[gravityview id="'.$atts['id'].'" search_field="'.$atts['searchfield'].'" search_value="'.$tomorrow_str.'"]' ;
    //$outstr .= $tomorrow_str;
    $outstr .=  do_shortcode($shortcode_str);
		return "{$outstr}";
	}
}
add_shortcode( 'gravityview_tomorrow', 'eai_gravityview_tomorrow' );
