<?php

namespace Drupal\share_light;

/**
 * Channel plugin for sharing on pinterest.
 */
class Pinterest extends ChannelBase {

  /**
   * Returns the channel's name.
   *
   * @return string
   *   Returns t('Pinterest').
   */
  public static function title() {
    return t('Pinterest');
  }

  /**
   * Returns the default values for the channel configuration.
   */
  public static function defaults() {
    return ['text' => ''] + parent::defaults();
  }

  /**
   * Generate configuration form elements for this channel.
   */
  public static function optionsWidget(array &$element, array $options) {
    $vars = ['@title' => static::title()];
    $element['text'] = [
      '#title' => t('Description text for @title.', $vars),
      '#description' => t('Description text for @title.', $vars),
      // Pinterest’s max-length for descriptions is 500.
      '#maxlength' => 500,
      '#type' => 'textarea',
      '#cols' => 60,
      '#rows' => 2,
      '#attributes' => [],
      '#default_value' => $options['text'],
    ];
  }

  /**
   * Returns data for a link element for sharing on facebook.
   *
   * @return array
   *   Options-array for a link renderable.
   */
  public function render() {
    // Get the url from the media object.
    if ($media_url = $this->block->getImageUrl()) {
      return [
        'title' => $this->title(),
        'href' => 'http://www.pinterest.com/pin/create/button/',
        'query' => [
          'url' => $this->generateShareUrl('pinterest_share'),
          'media' => $media_url,
          'description' => $this->options['text'],
        ],
        'attributes' => [
          'title' => t('Share this via Pinterest!'),
          'data-share' => 'pinterest',
          'target' => '_blank',
        ],
      ];
    }
  }

}
