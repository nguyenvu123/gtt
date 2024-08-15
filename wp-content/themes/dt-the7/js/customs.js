// A $( document ).ready() block.
(function ($) {
  'use strict';

$( document ).ready(function() {
  if($(window).width() < 768) {
    if($('.list-page-template li').hasClass('iactive')) {
      $('.list-page-template .active').parent().siblings().wrapAll('<ul class="dropdown"></ul>');
    }

    else {
      $('.list-page-template li:first-child').siblings().wrapAll('<ul class="dropdown"></ul>');
    }

    $('.list-page-template li > a').click(function(event) {
      event.preventDefault();
      $(this).closest('ul').toggleClass('active');
    });
  }
});
})(jQuery);