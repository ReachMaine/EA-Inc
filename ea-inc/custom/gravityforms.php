<?php
/* custom programing for gravity forms */

add_filter( 'gform_validation_1', 'eai_validate_useremail' );
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
