<?php

namespace Drupal\share_light;

class Pinterest implements ChannelInterface {
  public static function title() { return t('Pinterest'); }
  public function render($url, $options, $link) {
    $text = isset($options['advanced']['channel_pinterest_text']) ? $options['advanced']['channel_pinterest_text'] : '';

    // get the url from the media object
    $media_url = '';
    if (!empty($options['image']->type)) {
      if ($options['image']->type == 'image' && ($image = image_load($options['image']->uri))) {
        $media_url = file_create_url($image->source);
        return array(
          'title' => 'Pinterest',
          'href' => 'http://www.pinterest.com/pin/create/button/',
          'query' => array('url' => $url, 'media' => $media_url, 'description' => $text),
          'attributes' => array(
            'title' => t('Share this via Pinterest!'),
            'data-share' => 'pinterest',
          ),
        );
      }
    }
  }
}
