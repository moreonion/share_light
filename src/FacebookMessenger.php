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
    // Facebook strips tracking parameters, so don't add them.
    $url = $this->block->getUrl();

    return [
      'title' => $this->title(),
      'href' => 'fb-messenger://share',
      'query' => [
        'link' => $url,
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
