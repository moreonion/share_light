<?php

namespace Drupal\share_light;

/**
 * A channel for sharing via Twitter.
 */
class Twitter extends ChannelBase {

  /**
   * Returns the channel's name.
   *
   * @return string
   *   Returns t('Twitter').
   */
  public static function title() {
    return t('Twitter');
  }

  /**
   * Returns the default values for the channel's `optionsWidget`.
   */
  public static function defaults() {
    return array('text' => '') + parent::defaults();
  }

  /**
   * Adds a widget containing the options of the `Twitter` channel.
   *
   * Allows the user to enter a default Tweet text.
   */
  public static function optionsWidget(array &$element, array $options) {
    $title = static::title();
    $element['text'] = array(
      '#title' => t('Tweet text for @title.', ['@title' => $title]),
      '#description' => t('Tweet text for @title.', ['@title' => $title]),
      // 256 = 280 - 1 - 23 (tweet max-length - space - url in https)
      '#maxlength' => 256,
      '#type' => 'textarea',
      '#cols' => 60,
      '#rows' => 2,
      '#attributes' => array(),
      '#default_value' => $options['text'],
    );
  }

  /**
   * Returns a link field containing a link to `http://twitter.com/share`.
   */
  public function render() {
    $url = $this->block->getUrl();
    return array(
      'title' => 'Twitter',
      'href' => 'http://twitter.com/share',
      'query' => array('text' => $this->options['text'], 'url' => $url),
      'attributes' => array(
        'title' => t('Share this via Twitter!'),
        'data-share' => 'twitter',
      ),
    );
  }

}
