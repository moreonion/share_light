(function($) {
  Drupal.behaviors.share_light_tracking = {};
  Drupal.behaviors.share_light_tracking.attach = function(context, settings) {
    $('.share-light li a').on('click', function(event) {
      if (typeof ga !== 'undefined') {
        var $link = $(event.target);
        var channel = $link.data('share') ? $link.data('share') + " share" : event.target.title;
        ga('send', 'event', 'share', channel);
      }
    });
  }
})(jQuery);
