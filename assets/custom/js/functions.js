/**
 * make ugly select option form beautifully
 * @param {jQuery} $ 
 */
function operate_nice_select($) {
  $('select.shipping_method').niceSelect();
  $( document.body ).on({
    updated_cart_totals : function() {
      $('select.shipping_method').niceSelect();
    },
    init_checkout : function() {
      $('select.shipping_method').niceSelect();
    },
    updated_checkout : function() {
      $('select.shipping_method').niceSelect();
    }
  });
}

/**
 * help user enter easily self's phone number with nation code
 * @param {jQuery} $ 
 */
function operate_intlTelInput($) {
  var billing_phone_input = {
    default : $('#billing_phone'),
    national_format : $("#billing_phone_national_format")
  };

  if ( billing_phone_input.default.length > 0 && billing_phone_input.national_format.length > 0 ) {
    billing_phone_input.national_format.intlTelInput({
      initialCountry: "au",
      onlyCountries: ["au", "kr"],
      nationalMode: true
    });

    // 전화번호를 바탕으로 국가를 알아내서, 재설정!
    var national_format_number = billing_phone_input.default.val();
    var country = national_format_number.slice(1, 3);
    if ( country == "82" ) {
      billing_phone_input.national_format.intlTelInput("setCountry", "kr");
    } else {
      billing_phone_input.national_format.intlTelInput("setCountry", "au");
    }
    
    // listen to "keyup", but also "change" to update when the user selects a country
    billing_phone_input.national_format.on("keyup change", function() {
      var intlNumber = billing_phone_input.national_format.intlTelInput("getNumber");
      if (intlNumber) {
        billing_phone_input.default.val(intlNumber);
      }
    });

    var phone_number = billing_phone_input.national_format[0].attributes.value.nodeValue;
    billing_phone_input.national_format.val( phone_number );
  }
}

/**
 * open daum address search iframe
 */
function openDaumPostcode($, inputs) {
  daum.postcode.load(function(){
    new daum.Postcode({
      oncomplete: function(data) {
        // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
        // 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
        inputs.address_1.val(data.address);
        inputs.postcode.val(data.zonecode);
        inputs.address_2.focus();

        //전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
        //아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
        //var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
        //document.getElementById('addr').value = addr;
      }
    }).open();
  });
}

/**
 * remove clickToAddress settings
 * @param {jQuery} $
 * @param {jQuery p object} address_1_field
 */
function remove_clickToAddress_settings($, address_1_field) {
  // deinitiate clickToAddress
  var no_event_address_1_input = address_1_field.children('input').clone();
  no_event_address_1_input.attr('cc_applied', 'false');
  address_1_field.children('input').remove();
  address_1_field.append(no_event_address_1_input);

  $('#cc_c2a').remove();
}

/**
 * dynamically change address fields on account / checkout page.
 * @param {jQuery} $ 
 * @param {KR/AU} country 
 * @param {Shipping/Billing } type 
 */
function dynamically_change_address_fields($, country, type) {
  var required = '<abbr class="required" title="required">*</abbr>';
  var fields = {
    last_name : $('#'+type+'_last_name_field'),
    daum_address_search : $('#'+type+'_address_search_field'),
    custom_number     : $('#'+type+'_custom_number_field'),
  };

  var inputs = get_address_inputs(type);
  var first_name_label = inputs.first_name.prev();

  if ( country == 'KR' ) {
    first_name_label.empty();
    first_name_label.html('Name' + required);
    inputs.last_name.parent().css('display', 'none');
    fields.last_name.removeClass('validate-required');
    fields.daum_address_search.css('display', 'block');
    fields.custom_number.addClass('validate-required');
    fields.custom_number.css('display', 'block');

  } else if (country == 'AU') {
    first_name_label.empty();
    first_name_label.html('First name' + required);
    inputs.last_name.parent().css('display', 'block');
    inputs.last_name.prev().html('Last name'+required);
    fields.last_name.addClass('validate-required');
    fields.daum_address_search.css('display', 'none');
    fields.custom_number.removeClass('validate-required');
    fields.custom_number.css('display', 'none');
  }
}

function get_address_inputs(type) {
  var inputs = {
    first_name      : $('#'+type+'_first_name'),
    last_name       : $('#'+type+'_last_name'),
    address_1       : $('#'+type+'_address_1'),
    address_2       : $('#'+type+'_address_2'),
    city            : $('#'+type+'_city'),
    state           : $('#'+type+'_state'),
    postcode        : $('#'+type+'_postcode'),
  };
  return inputs;
}

/**
 * add postcode search window open listener to btn
 * @param {jQuery} $ 
 * @param {postcode_search button} btn
 */
function add_postcode_search_open_listener($, btn) {
  btn.on('click', function(){
    var self = $(this);
    var type = 'billing';
    if ( self.parent().parent().prop('id') == 'shipping_address_search_field' ) {
      type = 'shipping';
    }
    var inputs = {
      postcode  : $('#'+type+'_postcode'),
      address_1 : $('#'+type+'_address_1'),
      address_2 : $('#'+type+'_address_2'),
    };
    openDaumPostcode($, inputs);
  });
}

function empty_address_inputs(inputs) {
  inputs.first_name.val('');
  inputs.last_name.val('');
  inputs.address_1.val('');
  inputs.address_2.val('');
  inputs.city.val('');
  inputs.postcode.val('');
}

function get_c2a_obj(type) {
  if ( typeof window.cta == "undefined" ) {
    window.cta = new clickToAddress({
      accessToken: '2f2a5-35437-5ed5e-0280f', // Replace this with your access token
      defaultCountry: 'aus',
      countrySelector: false,
      getIpLocation: false,
      onError: function(code, message){
        // Perform any action here with the available data.
        // For example, you may want to reveal the form inputs if there is an error.
        console.dir(code);
        console.dir(message);
      },
    });
  }
  var inputs = get_address_inputs(type);
  window.cta.onResultSelected = function(c2a, elements, address){
    // Perform any action here with the available data.
    // For example, you could reveal all the form inputs when the inputs are filled.
    var line_1 = address.line_1 + ' ' + address.line_2;
    inputs.address_1.val(line_1);
    inputs.state.val(address.province_code);
    inputs.state.trigger('change');
    inputs.postcode.val(address.postal_code);
    inputs.city.val(address.locality);
    inputs.address_2.focus();
  };
  return window.cta;
}

function init_c2a_globally() {
  window.cta = new clickToAddress({
    accessToken: '2f2a5-35437-5ed5e-0280f', // Replace this with your access token
    defaultCountry: 'aus',
    countrySelector: false,
    getIpLocation: false,
    onError: function(code, message){
      // Perform any action here with the available data.
      // For example, you may want to reveal the form inputs if there is an error.
      console.dir(code);
      console.dir(message);
    },
  });
}

function add_c2a_to_address_input(type) {
  $( "#"+type+"_address_1" ).on('focus', function(){
    var cc_c2a = $('#cc_c2a');
    cc_c2a.addClass('d-none-f');
    if ( $("#"+type+"_country").find(':selected').val() == 'KR' ) {
      console.log( 'yes now is korea' );
      return;
    }
    cc_c2a.removeClass('d-none-f');
    var dom = {
      search: type+'_address_1', // 'search_field' is the name of the search box element
    };
    var cta = get_c2a_obj(type);
    if ( typeof $('#'+type+'_address_1').attr("cc_applied") == 'undefined' ) {
      cta.attach(dom);
    }
  });
}