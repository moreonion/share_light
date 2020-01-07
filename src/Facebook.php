<?php

namespace Drupal\share_light;

/**
 * Channel plugin for facebook sharing.
 */
class Facebook extends ChannelBase {

  /**
   * Returns the channel's name.
   *
   * @return string
   *   Returns t('Facebook').
   */
  public static function title() {
    return t('Facebook');
  }

  /**
   * Returns data for a link element for sharing on facebook.
   *
   * @return array
   *   Options-array for a link renderable.
   */
  public function render() {
    return [
      'title' => $this->title(),
      'href' => 'https://www.facebook.com/sharer.php',
      'query' => ['u' => $this->block->getUrl()],
      'attributes' => [
        'title' => t('Share this via Facebook!'),
        'data-share' => 'facebook',
        'target' => '_blank',
      ],
    ];
  }

}
