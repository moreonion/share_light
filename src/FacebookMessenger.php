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
   * @return array|null
   *   The link field's renderable array or NULL if generation
   *   of the URL to share failed.
   */
  public function render() {
    $url = $this->generateShareUrl('fbmsg_share');

    if ($url) {
      return [
        'title' => 'Facebook Messenger',
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

}
