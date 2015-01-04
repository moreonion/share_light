<?php

namespace Drupal\share_light;

class Facebook implements ChannelInterface {
  public static function title() { return t('Facebook'); }
  public function render($url, $options, $link) {
    return array(
      'title' => 'Facebook',
      'href' => 'https://www.facebook.com/sharer.php',
      'query' => array('u' => urlencode($url)),
      'attributes' => array(
        'title' => t('Share this via Facebook!'),
        'data-share' => 'facebook',
      ),
    );
  }
}
