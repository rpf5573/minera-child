(function($) {

  "use strict";
  var HT = {};
  /*CUSTOM=======================================================*/
  /*quantity add to cart*/
  HT.product_quantity_add_to_cart = function(){
      $(".w-minus").on('click', function(){
          var $input = $(this).parent().find('input'),
              currVal = parseInt($input.val(), 10);
          if(currVal > 1) $input.val(currVal-1);
          /*cart page: enable button update cart when click*/
          $("[name='update_cart']").prop("disabled", false);
      });
      $(".w-plus").on('click', function(){
          var $input = $(this).parent().find('input'),
              currVal = parseInt($input.val(), 10),
              max = parseInt($input.prop('max'));
              if(currVal == max){
                  return;
              }
          $input.val(currVal+1);
          /*cart page: enable button update cart when click*/
          $("[name='update_cart']").prop("disabled", false);
      });
  }

  /*product carousel*/
  HT.product_carousel = function($el, _data){
      /*do not run script when `layout` option in product single page set to `list image`*/
      if(_data == false && !$el.hasClass('has-product-slider')) return;

      var HTvertical = false,
          HTverticalSwiping = false,
          HTvariableWidth = true,
          Rtl = false;

      if($(document.body).hasClass('rtl')){
          Rtl = true;
      }
      if($el.hasClass('gallery-vertical')){
          HTvertical = true;
          HTverticalSwiping = true;
          HTvariableWidth = false;
          Rtl = false;
      }

      /*main carousel*/
      $el.find('.w-img-crs').slick({
          infinite: false,
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: true,
          fade: false,
          asNavFor: $el.find('.w-thumb-crs'),
          vertical: HTvertical,
          variableWidth: HTvariableWidth,
          verticalSwiping: HTverticalSwiping,
          rtl: Rtl,
          responsive: [
              {
                  breakpoint: 991,
                  settings: {
                      vertical: false,
                      verticalSwiping: false,
                      variableWidth: true,
                      arrows: false
                  }
              },
              {
                  breakpoint: 600,
                  settings: {
                      vertical: false,
                      verticalSwiping: false,
                      variableWidth: false,
                      arrows: false
                  }
              }
          ]
      });
      /*thumb carousel*/
      $el.find('.w-thumb-crs').slick({
          infinite: false,
          slidesToShow: 5,
          slidesToScroll: 1,
          dots: false,
          arrows: false,
          focusOnSelect: true,
          asNavFor: $el.find('.w-img-crs'),
          vertical: HTvertical,
          variableWidth: HTvariableWidth,
          verticalSwiping: HTverticalSwiping,
          rtl: Rtl,
          responsive: [
              {
                  breakpoint: 991,
                  settings: {
                      vertical: false,
                      verticalSwiping: false,
                      variableWidth: true
                  }
              }
          ]
      });
  }

  /*destroy and reInit easyzoom function*/
  HT.image_variation_zoom = function(){
      if($('.single-product').length && $('.w-images-box.w-layout-slider').length && $('.ez-zoom').length){
          var $easyzoom = $(document.body).find('.w-img-item:eq(0)').easyZoom(),
              api = $easyzoom.data('easyZoom');
          api.teardown();
          api._init();
      }
  }

  /*product carousel: go to first slide image*/
  HT.gotofirst = function(){
      if($('#ht-quick-view-popup.ht-qvp-open').length){
          $( '#ht-quick-view-popup .w-img-crs.slick-initialized' ).slick( 'slickGoTo', 0 );
      }else{
          $('#main .w-img-crs.slick-initialized' ).slick( 'slickGoTo', 0 );
      }
  }

  /*product variation*/
  HT.product_variation = function(){
      /*woocommerce: product variation*/
      var $_product = $(document.body).find('.product'),

          /*product image*/
          $_image = $_product.find('.w-img-item:eq(0)'),
          $_image_src = $_image.find('img').prop('src'),

          /*easy zoom attr*/
          $_zoom = $_image.data('zoom'),

          /*product thumbnail*/
          $_thumb = $_product.find('.w-thumb-item:eq(0)'),
          $_thumb_src = $_thumb.find('img').prop('src');

          /*reset easyzoom function*/
          HT.image_variation_zoom();

      /*event when variation changed=========*/
      $(document).on( 'found_variation', 'form.variations_form', function( event, variation ) {
          /*get image url form `variation`*/
          var img_url = variation.image.full_src;
          var thumb_url = variation.image.thumb_src;

          /*change `src` image*/
          $_image.find('img').prop('src', img_url);
          $_thumb.find('img').prop('src', thumb_url);
          $_image.attr('data-zoom', img_url);

          /*callback zoom function*/
          HT.image_variation_zoom();

          /*go to first slick slide*/
          HT.gotofirst();
      });

      /*reset variation========*/
      $('.reset_variations').on('click', function(e){
          e.preventDefault();

          /*change `src` image*/
          $_image.find('img').prop('src', $_image_src);
          $_thumb.find('img').prop('src', $_thumb_src);
          $_image.attr('data-zoom', $_zoom);

          /*callback zoom function*/
          HT.image_variation_zoom();

          /*go to first slick slide*/
          HT.gotofirst();
      });
  }

  /*mega menu*/
  HT.mega_menu = function() {
      if($('.mega-menu-row').length == 0) return;

      var screenWidth = $(window).width();
      if (screenWidth < 992) return;
      
      $('.mega-menu-row').each(function() {
          var $col = $('.mega-menu-col', this).length,
              $this = $(this),
              offset = $this.closest('.menu-item-has-mega-menu').offset().left,
              code = function(){
                  $this.addClass('mega-changed-position').removeClass('mega-static');
              },
              code_2 = function(){
                  $this.parent().addClass('mega-static');
              }

          if($col == 1){
              var $class = 'mega-1-col',
                  $width = 250;
          }else if($col == 2){
              var $class = 'mega-2-col',
                  $width = 450;
          }else if($col == 3){
              var $class = 'mega-3-col',
                  $width = 725;
          }else{
              var $class = 'mega-standard';
              $this.parent().addClass('mega-static');
          }

          $this.addClass($class);
          if((screenWidth - offset) < $width){
              if(offset < $width){
                  code_2();
              }else{
                  code();
              }
          }
      });
  }

  /*shop switch style*/
  $(document.body).on('click', '.btn-switcher', function () {
      var $el = $(this),
          style = $el.data('style'),
          block = $el.closest('.theme-product-block');

      $el.addClass('active').siblings().removeClass('active');
      block.removeClass('shop-view-grid shop-view-list').addClass('shop-view-' + style);
  });

  /*loading effect*/
  if($('.is-page-loading').length){
      NProgress.configure({
          template: '<div class="bar" role="bar"></div>',
          parent: '#page-loader',
          showSpinner: true,
          easing: 'ease',
          minimum: 0.3,
          speed: 500,
      });

      NProgress.start();
  }

  /*shop - swatches list*/
  HT.swatches_list = function(){
      $(document.body).on('click', '.p-attr-swatch', function(){
          var src,
              t = $(this),
              variation_image_src = t.data('src'),
              product = t.closest('.p-col'),
              p_image = product.find('.p-image'),
              view_image = p_image.find('img'),
              default_view_image_src = product.find('.p-image').data('ori_src');

          if(t.hasClass('active')){
              src = default_view_image_src;
              t.removeClass('active');
          }else{
              src = variation_image_src;   
              t.addClass('active').siblings().removeClass('active');
          }


          if( view_image.prop('src') == src ) return;

          p_image.addClass('image-is-loading');

          view_image.prop('src', src).one('load', function(){
              p_image.removeClass('image-is-loading');
          });
      });
  }

  /*QUICK VIEW CLOSE ACTION*/
  HT.qv_close = function(){
      var qv_popup = $(document.body).find( '#ht-quick-view-popup' ),
          qv_overlay = qv_popup.find( '.ht-qvo'),
          qv_close_btn = qv_popup.find( '#ht-qvc' );

      $(document.body).removeClass('quickview-on');
      qv_popup.removeClass('ht-qvp-open').addClass('ht-qvp-ready');
      /*remove content append()*/
      setTimeout(function(){
          qv_popup.find('.product').remove();
      }, 400);
  }

  /* support premium plugin `woocommerce-subscription`
      ------------------------------------------------->*/
  HT.product_type_subscription = function(){
      var _btn = $(document.body).find('.single_add_to_cart_button'),
          _product = _btn.closest('.product');
      if(_product.hasClass('product-type-subscription') || _product.hasClass('product-type-variable-subscription')){
          _btn.addClass('subscription-btn-not-ajax');
      }
  }

  /*AJAX - PRODUCT QUICK VIEW*/
  HT.product_quick_view_content = function(){
      if($('#ht-quick-view-popup').length == 0) return;

      var $popup = $(document.body).find('#ht-quick-view-popup'),
          $quickview_box = $popup.find('.quick-view-box'),

      /*QUICK VIEW CLOSE ACTION*/
          qv_overlay = $popup.find( '.ht-qvo'),
          qv_close_btn = $popup.find( '#ht-qvc' );

          /*Close box by click overlay*/
          qv_overlay.on( 'click', function(e){
              HT.qv_close();
          });

          /*Close box by click `ESC` key*/
          $(document.body).keyup(function(e){
              if( e.keyCode === 27 )
                  HT.qv_close();
          });

          /*Close box by click close button*/
          qv_close_btn.on( 'click', function(e) {
              e.preventDefault();
              HT.qv_close();
          });
      /*END QUICK VIEW CLOSE ACTION*/

      $(document.body).on('click', '.quick-view-btn', function(e){
          e.preventDefault();

          $popup.addClass('ht-qvp-loading');
          $(document.body).addClass('quickview-on');

          var id = $(this).data('id');

          $.ajax({
              type: 'POST',
              url: admin_ajax.url,
              data: {
                  action: 'quickview_action',
                  product_id: id
              },
              success: function(data){

                  /*PRODUCT VARIATION*/
                  var $content = $(data),
                      $form = $content.find('.variations_form');

                      if($(data).find('#product-lightbox').length){
                          /*disable click to image link on quickview*/
                          $(document.body).on('click', '#product-lightbox a.w-img-item', function(e){
                              e.preventDefault();
                          });
                      }

                  $quickview_box.append($content);

                  $popup.removeClass('ht-qvp-loading').addClass('ht-qvp-open');

                  /*woocommerce variaion params*/
                  if (typeof wc_add_to_cart_variation_params !== 'undefined') {
                      $form.wc_variation_form();
                      $form.find('.variations select').change();
                  }

                  /*plugin: watches variation*/
                  if (typeof $.fn.tawcvs_variation_swatches_form !== 'undefined') {
                      $form.tawcvs_variation_swatches_form();
                  }

                  /*OTHER ACTION*/
                  HT.product_carousel($('#ht-quick-view-popup'), true);
                  HT.product_quantity_add_to_cart();
                  HT.product_variation();
              },
              complete: function(){
                  HT.product_type_subscription();
              }
          });            
      });
  }

  /*AJAX - BLOG LOAD MORE BUTTON*/
  HT.blog_load_more_btn = function(){
      if($('.blog-data.data-btn').length){
          $('.btn-load-more-post').on('click', function(e){
              e.preventDefault();

              var button = $(this);
              button.addClass('btn-is-loading');

              $.ajax({
                  url: admin_ajax.url,
                  type: 'post',
                  data: {
                      action: 'blog_posts_action',
                      query: blog_posts_data.posts,
                      page: blog_posts_data.current_page
                  },
                  beforeSend: function(xhr) {
                      button.text(blog_posts_data.loading_text);
                  },
                  success: function(data){
                      if(data){
                          button.text(blog_posts_data.load_more_posts).removeClass('btn-is-loading').prev().after(data);
                          blog_posts_data.current_page++;

                          if(blog_posts_data.current_page == blog_posts_data.max_page){
                              button.addClass('is-done').text(blog_posts_data.no_posts_found).off();
                          }
                      }else{
                          button.addClass('is-done').text(blog_posts_data.no_posts_found).off();
                      }
                  }
              });
          });
      }
  }

  /*AJAX - BLOG LOAD MORE INFINITE SCROLL*/
  if($('.blog-data.data-scroll').length){
      var status = true,
          el = $('.blog-data.data-scroll'),
          loading = el.find('.el-loading');
  }
  HT.blog_infinite_scroll = function(){
      if($('.blog-data.data-scroll').length){
          $('.el-loading').inViewport(function(){
              if(status == true ){
                  $.ajax({
                      url : admin_ajax.url,
                      data: {
                          action: 'blog_posts_action',
                          query: blog_posts_data.posts,
                          page : blog_posts_data.current_page
                      },
                      type: 'POST',
                      beforeSend: function( xhr ){
                          status = false;
                          loading.text(blog_posts_data.loading_text);
                          setTimeout(function(){
                              loading.addClass('is-visible');
                          }, 300);
                      },
                      success: function(data){
                          if(data) {
                              /*where to insert data*/
                              setTimeout(function(){
                                  el.find('.blog-article:last-of-type').after(data);
                              }, 500);

                              /*when ajax is completed, we can run it again*/
                              status = true;
                              blog_posts_data.current_page++;

                              /*detect when loaded all posts*/
                              if(blog_posts_data.current_page == blog_posts_data.max_page){
                                  loading.text(blog_posts_data.no_posts_found).addClass('is-done');
                                  /*remove loading animation element*/
                                  setTimeout(function(){
                                      el.find('.el-loading').remove();
                                  }, 500);
                              }
                          }
                          setTimeout(function(){
                              loading.removeClass('is-visible');
                          }, 300);
                      }
                  });
              }
          });
      }
  }

  /*AJAX - SINGLE ADD TO CART*/
  HT.single_add_to_cart = function(){
      HT.product_type_subscription();
      
      $(document.body).on('click', '.single_add_to_cart_button',function(e){
          if($(this).hasClass('subscription-btn-not-ajax')) return;
          e.preventDefault();

          if($(this).hasClass('disabled')) return;

          var t = $(this),
              _stock_qty = t.data('stock_qty') || 0,
              _in_cart_qty,
              _input = t.prev(),/*input value for product quantity in cart*/
              _out_of_stock = _input.data('out_of_stock'),
              _not_enough = _input.data('not_enough'),
              _form = t.closest('form.cart'),/*detect form*/
              var_form = t.closest('form.variations_form'),/*detect variations form*/
              id_var = var_form.find('input[name="variation_id"]').val(),/*get product variation id*/
              input_point = _form.find('input[name="mwb_wpr_pro_cost_to_points"]'),

              $cart_content = $(document.body).find('.cart-sidebar-content'),/*cart sidebar content*/
              $total_item = $(document.body).find('.theme-shopping-cart .counter-cart');/*total items cart*/

          if(var_form.length){/*variations product*/
              /*check variation id*/
              if(id_var === '0' || id_var === '') return;

              var _data,
                  item = {},
                  id = var_form.find('input[name=product_id]').val(),
                  qty = var_form.find('input[name="quantity"]').val();

                  var_form.find('select[name^=attribute]').each(function(){
                      var name = $(this).prop('name'),
                          value = $(this).val();

                      item[name] = value;
                  });
                  _data = {action: 'atc', product_id: id, variation_id: id_var, quantity: qty, variations: item};
          }else{/*simple product*/
              var id = t.val(),
                  qty = _form.find('input[name="quantity"]').val(),
                  _data = {action: 'atc', product_id: id, quantity: qty};

              /*check out of stock*/
              if(_out_of_stock != '' && _input.val() >= _stock_qty){
                  alert(_out_of_stock);
                  return;
              }

              /*when input quantity > stock quantity*/
              if(_out_of_stock != '' && +_input.val() + +qty > _stock_qty){
                  alert(_not_enough);
                  return;
              }
          }

          if ( input_point.is(':checked') ) {
            alert('use point to discount!');
            _data.mwb_wpr_pro_cost_to_points = input_point.val();
          }
              
          $.ajax({
              type: 'POST',
              url: admin_ajax.url,
              data: _data,
              beforeSend: function(){
                  t.addClass('adding-to-cart');
                  $cart_content.html('');
              },
              success: function(data){
                  t.removeClass('adding-to-cart');
                  var count = $(data).filter('span.count').html(),
                      _in_cart_qty = $(data).filter('span.count').data('current_item_in_cart');/*get product quantity in ajax*/
                      _input.val(_in_cart_qty);/*update input value*/
                  $total_item.text(count);
                  $cart_content.html(data);
              },
              complete: function(){
                  $cart_content.find('span.count').remove();
                  $(document.body).addClass('cart-sidebar-opened');
                  HT.qv_close();
              }
          });
          
      });
  }

  /*OPEN CART SIDEBAR*/
  $('.shopping-cart-icon').on('click', function(e){
      e.preventDefault();

      if($('.woocommerce-cart.woocommerce-page').length) return;/*not show cart sidebar on `cart` page*/
      $(document.body).addClass('cart-sidebar-opened');
  });
  $(document.body).on('added_to_cart', function(){
      $(this).addClass('cart-sidebar-opened');
  });

  /*CLOSE CART SIDEBAR*/
  $('.ht-cart-overlay, .cart-sidebar-close-btn').on('click', function(){
      $(document.body).removeClass('cart-sidebar-opened');
  });

  /*REMOVE ITEM CART ANIMATION*/
  $(document.body).on('click', '.remove.remove_from_cart_button',function(e){
      e.preventDefault();
      $(this).closest('.cart-sidebar-content').addClass('cart-removing');
  });

  /*woocommerce ordering shop page*/
  HT.woo_top_sort_oder = function(){
      if($('.woocommerce-ordering .orderby').length){
          if($(window).width() > 480){
              $('.woocommerce-ordering .orderby').select2({
                  width: 230
              });
          }else{
              $('.woocommerce-ordering .orderby').select2({
                  width: '100%'
              });
          }
      }
  }

  /*READY =======================================================*/
  $(document).ready(function() {
      HT.woo_top_sort_oder();
      HT.swatches_list();
      HT.single_add_to_cart();
      HT.blog_load_more_btn();

      /* landing shortcode */
      $(document.body).on('mouseenter', '.fte-sc', function(){
          $(document.body).find('.fte-sc').addClass('fte-faded');
          $(this).removeClass('fte-faded');
      }).on('mouseleave', '.fte-sc', function(){
          $(document.body).find('.fte-sc').removeClass('fte-faded');
      });

      /*search form button*/
      var search_form    = $(document.body).find( '#search-form-content' ),
          search_form_textfield = search_form.find('#ht-search-field'),

          /*action when quickview close*/
          search_closed_action = function() {
              $(document.body).removeClass('search-form-on');
              search_form.removeClass('search-form-open');
              search_form_textfield.val('');
          }

      $('#ht-search-btn, #sticky-search-btn').on('click', function(){
          search_form.addClass('search-form-open');
          search_form_textfield.focus();
          $(document.body).addClass('search-form-on');
      });

      /*Close box by click overlay*/
      search_form.on( 'click', function(e){
          if (e.target !== this) return;
          search_closed_action();
      });

      /*Close box by click `ESC` key*/
      $(document).keyup(function(e){
          if( e.keyCode === 27 || e.keyCode === 13)
              search_closed_action();
      });
      
      /*woocommerce: product variation*/
      HT.product_variation();

      /*woocommerce: event when update cart total*/
      $( document.body ).on( 'updated_cart_totals', function() {
          HT.product_quantity_add_to_cart();
      });

      /*woocommerce single: quantity add to cart*/
      HT.product_quantity_add_to_cart();

      /*woocomerce quick view*/
      HT.product_quick_view_content();

      /*plyr setup: video post format*/
      if((document.body.className.match('.single-format-video'))){
          plyr.setup();
      }

      /*popup video shortcode*/
      $('.vd-play').on('click', function(e){
          var className = e.target.className;
          ~className.indexOf('vimeo') && BigPicture({
              el: e.target,
              vimeoSrc: e.target.getAttribute('vimeoSrc')
          });
          ~className.indexOf('youtube') && BigPicture({
              el: e.target,
              ytSrc: e.target.getAttribute('ytSrc')
          });
      });

      /*topbar mobile*/
      $('#topbar-toggle').on('click', function () {
          $('.theme-topbar').slideToggle('fast');
      });

      /*wishlist button onclick*/
      $('.p-wl-btn').on('click', function(){
          var that = $(this);
          that.addClass('p-wl-btn-clicked');
          setTimeout(function(){
              that.addClass('p-wl-btn-done');
          }, 2000);
      });

      /*scroll to top click button*/
      $(".scroll-to-top").on('click', function () {
          $('html, body').animate({scrollTop: 0}, 300);
      });
  });

  /*BEFORE UNLOAD ===============================================*/
  $(window).on('beforeunload', function () {
      if($('.is-page-loading').length){
          $('#theme-container').addClass('is-loading').removeClass('is-ready');
      }
  });

  /*PAGE SHOW ========================================================*/
  window.onpageshow = function(event) {
      /*disable back-forward cache on safari*/
      if (event.persisted && navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1){
          window.location.reload();
      }
  };

  /*LOAD ========================================================*/
  $(window).on('load', function(){
      /*loading effect*/
      if($('.is-page-loading').length){
          $('#theme-container').addClass('is-ready').removeClass('is-loading');
          NProgress.done();
      }

      /*blog masonry*/
      if(document.getElementsByClassName("blog-news-masonry").length){
          var msnry = new Masonry( '.blog-news-masonry', {
              itemSelector: '.blog-article',
              gutter: '.gutter-sizer'
          });
      }

      /*mega menu*/
      HT.mega_menu();

      /*woocommerce carousel image single*/
      HT.product_carousel($('#main'), false);

      /*easy zoom woocommerce image single*/
      if($('.ez-zoom').length){
          enquire.register("screen and (min-width:992px)", {
              match: function() {
                  $('.ez-zoom').easyZoom();
              },
              unmatch : function() {
                  $('.ez-zoom').easyZoom().data('easyZoom').teardown();
              }
          });
      }

      /*testi shortcode*/
      $('.theme-testi').slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: false,
          arrows: true,
          dots: false,
          fade: false,
      });

      /*post format gallery*/
      $(".pf-gallery").slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: true,
          dots: true,
          infinite: false,
          fade: false,
          adaptiveHeight: true
      });

      /*MENU MOBILE*/
      enquire.register("screen and (max-width:991px)", {
          match: function() {
              $('.theme-menu-responsive').ht_menu({
                  resizeWidth: '991',
                  initiallyVisible: false,
                  animSpeed: 'fast',
                  easingEffect: 'linear'
              });
          }
      });
      
      /*TOGGLE SIDER MENU*/
      if($('#sidebar-menu-btn').length){
          $(document.body).addClass('has-sidebar-menu');
          $(document.body).on('click', "#sidebar-menu-btn, .sb-panel-overlay",function() {
              $("#sidebar-menu-btn, .sb-panel-overlay, .box-sidebar-menu").toggleClass("active");
              /* Check panel overlay */
              if ($(".sb-panel-overlay").hasClass("active")) {
                  $(".sb-panel-overlay").fadeIn();
                  $(document.body).addClass('sidebar-menu-opened');
              } else {
                  $(".sb-panel-overlay").fadeOut();
                  $(document.body).removeClass('sidebar-menu-opened');
              }
          });
      }

      /*SIDEBAR MENU*/
      enquire.register("screen and (min-width:992px)", {
          match: function() {
              $(".theme-sidebar-menu").accordion();
          }
      });

      /*carousel image*/
      $('.theme-imgs-crs').slick({
          slidesToShow: 4,
          slidesToScroll: 1,
          infinite: true,
          arrows: true,
          dots: false,
          fade: false,
          variableWidth: true,
          responsive: [
              {
                  breakpoint: 992,
                  settings: {
                      slidesToShow: 2,
                      variableWidth: false,
                  }
              },
              {
                  breakpoint: 600,
                  settings: {
                      slidesToShow: 1,
                      centerMode: true,
                      variableWidth: false,
                  }
              }
          ]
      });

      /*loading effect*/
      $("#p-loading").fadeOut("slow");
      $(".p-circular").fadeOut();
      
      /*detect the last comment in comment list*/
      $('.comment-list .comment-item:last').addClass('the-last-comment');
  });
  
  /*RESIZE ======================================================*/
  $(window).on('resize', function(){
      HT.woo_top_sort_oder();
      HT.mega_menu();
  });

  /* SCROLL =====================================================*/
  $(window).on('scroll', function () {
      HT.blog_infinite_scroll();
      /*scroll to top scroll action*/
      if ($(this).scrollTop() > 100) {
          $('.scroll-to-top').addClass('scroll-visible');
      } else {
          $('.scroll-to-top').removeClass('scroll-visible');
      }

      /*sticky menu on mobile*/
      if($(document.body).hasClass('has-mobile-sticky-menu')){
          if ($(this).scrollTop() > 46){
              $(document.body).addClass('sticky-mobile');
          }else{
              $(document.body).removeClass('sticky-mobile');
          }
      }
  });
})(jQuery);