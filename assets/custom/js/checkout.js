jQuery(document).ready(function(){

  // from functions.js
  // operate_nice_select(jQuery);

  // from functions.js
  operate_intlTelInput(jQuery);

  // $(document.body).on('updated_checkout', function () {
  //   var tip = $('.shipping_method option:selected').data('tip');
  //   $('.balloon').attr('data-balloon', tip);
  // });

  add_postcode_search_open_listener(jQuery, $('.postcode_search'));

  $( document.body ).bind( 'country_to_state_changed', function( event, country, wrapper ) {
    if ( wrapper.hasClass('woocommerce-billing-fields') ) {
      dynamically_change_address_fields(jQuery, country, 'billing');
    }
    if ( wrapper.hasClass('woocommerce-shipping-fields') ) {
      dynamically_change_address_fields(jQuery, country, 'shipping');
    }
  });

  $('#repute_sms_send_me_sms_order_status_updates').change(function(){
    var self = $(this);
  });

  $( document.body ).on('click', 'a.showpoint', function(){
    $('.rpf-discount_point').slideToggle(400);
  });

  $('.rpf-discount_point__apply__btn').on('click', function(){
    $('.woocommerce').block({
      message: null,
      overlayCSS: {
        background: '#fff',
        opacity: 0.6
      }
    });
    var input = {
      $point : $('.rpf-discount_point__apply__input'),
      $nonce : $('.rpf-discount_point #rpf_nonce')
    };
    var data = {
      discount_point : input.$point.val(),
      rpf_nonce : input.$nonce.val(),
      action : 'rpf_save_discount_point_to_session'
    };
    $.post(ajaxData.url,
      data,
      function (data, textStatus, jqXHR) {
        console.dir(data);
        $('.woocommerce').unblock();
        if (data.success) {
          $( document.body ).trigger( 'update_checkout', { update_shipping_method: false } );
        } else {
          alert( 'error on sending your discount point to server!' );
        }
      }
    );
  });

  $(document.body).on('applied_coupon', function(){
    alert('applied_coupon is called');
  });

  init_c2a_globally();

  add_c2a_to_address_input('billing');

  add_c2a_to_address_input('shipping');

});