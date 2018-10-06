jQuery(document).ready(function(){

  /**
   * Show selected image from modal to img tag
   */
  (function($){
    function readURL(input, img) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          img.attr('src', e.target.result);
          img.attr('srcset', null);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
    $(".user_avatar__uploader > input").change(function() {
      console.dir( 'image changed' );
      readURL(this, $('.user_avatar__avatar > img'));
    });
  })(jQuery);

  // from functions.js
  // operate_intlTelInput(jQuery);

  /**
   * Disable browser's autofill feature
   */
  (function($){
    $('#password_current').val('');
  })(jQuery);

  add_postcode_search_open_listener(jQuery, $('.postcode_search'));

  $( document.body ).bind( 'country_to_state_changed', function( event, country, wrapper ) {
    var type = 'billing';
    if ( $('#shipping_first_name_field').length > 0 ) {
      type = 'shipping';
    }
    dynamically_change_address_fields(jQuery, country, type);
  });

  init_c2a_globally();

  if ( $('#shipping_first_name_field').length > 0 ) {
    add_c2a_to_address_input('shipping');
  } else {
    add_c2a_to_address_input('billing'); 
  }

});