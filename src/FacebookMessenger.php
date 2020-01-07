<?php

namespace Drupal\share_light;

/**
 * A channel for sharing via `Facebook Messenger`.
 */
class FacebookMessenger extends ChannelBase {

  /**
   * Returns the channel's name.
   *
   * @return string
   *   Returns t('Facebook Messenger')
   */
  public static function title() {
    return t('Facebook Messenger');
  }

  /**
   * Returns a link field contaiing a `fb-messenger:` URI.
   *
   * @return array
   *   The link field's renderable array.
   */
  public function render() {
    return [
      'title' => $this->title(),
      'href' => 'fb-messenger://share',
      'query' => [
        'link' => $this->block->getUrl(),
      ],
      'attributes' => [
        'title' => t('Share this via Facebook Messenger!'),
        'data-share' => 'facebook-messenger',
        'class' => ['mobile'],
      ],
      'external' => TRUE,
    ];
  }

}
