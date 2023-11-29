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
    return array('text' => '[share:url]') + parent::defaults();
  }

  /**
   * Adds configuration options for the `Twitter` channel to the field widget.
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
   * Returns a link field containing a link to `https://twitter.com/intent/tweet`.
   *
   * @return array
   *   The link field's renderable array.
   */
  public function render() {
    $data = $this->generateTokenData('twitter_share');
    $text_includes_url = strpos($this->options['text'], '[share:url]') !== FALSE;
    $text = token_replace($this->options['text'], $data);

    return array(
      'title' => $this->title(),
      'href' => 'https://twitter.com/intent/tweet',
      'query' => [
        'text' => $text,
        'url' => !$text_includes_url ? $this->generateShareUrl('twitter_share') : '',
      ],
      'attributes' => array(
        'title' => t('Share this via Twitter!'),
        'data-share' => 'twitter',
        'target' => '_blank',
      ),
    );
  }

}
