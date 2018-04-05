(function($) {
  Drupal.behaviors.share_light_hide_mobile_buttons = {};
  Drupal.behaviors.share_light_hide_mobile_buttons.attach = function(context, settings) {
    // @see https://stackoverflow.com/questions/3514784/what-is-the-best-way-to-detect-a-mobile-device-in-jquery#3540295
    if( ! /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobi/i.test(navigator.userAgent) ) {
      $('.share-light .mobile', context).parent().hide();
    }
  }
})(jQuery);
