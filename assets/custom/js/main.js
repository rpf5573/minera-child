jQuery(document).ready(function () {
  (function($){
    var page_widht = $('html').width();
    // 데스크탑 혹은 테블릿 에서만 Sticky를 적용시킵니다
    if (page_widht > 992) {
      $('.widget-area.side').sticky({
        topSpacing : 80
      });
      $('.home .header-box').sticky({
        topSpacing : 0
      });
    }

    $header_layout_1 = $('.header-layout-1 > .header-box');
    $window = $(window);
    $document = $(document);
    windowHeight = $window.height();
    window.addEventListener('scroll', function(){
      var scrollTop = $window.scrollTop();
      if ( scrollTop > 100 ) {
        $header_layout_1.addClass('minimize');
      } else {
        $header_layout_1.removeClass('minimize');
      }
    }, false);

  })(jQuery);
});