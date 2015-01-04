<?php

namespace Drupal\share_light;

class Twitter implements ChannelInterface {
  public static function title() { return t('Twitter'); }
  public function render($url, $options, $link) {
    $text = isset($options['advanced']['channel_twitter_text']) ? $options['advanced']['channel_twitter_text'] : '';
    return array(
      'title' => 'Twitter',
      'href' => 'http://twitter.com/share',
      'query' => array('text' => $text, 'url' => $url),
      'attributes' => array(
        'title' => t('Share this via Twitter!'),
        'data-share' => 'twitter',
      ),
    );
  }
}
