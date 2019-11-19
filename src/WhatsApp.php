<?php

namespace Drupal\share_light;

/**
 * A channel for sharing via `WhatsApp`.
 */
class WhatsApp extends ChannelBase {

  /**
   * Returns the channel's name.
   *
   * @return string
   *   Returns t('WhatsApp').
   */
  public static function title() {
    return t('WhatsApp');
  }

  /**
   * Returns the default values for the channel's `optionsWidget`.
   */
  public static function defaults() {
    return ['text' => '[share:url]'] + parent::defaults();
  }

  /**
   * Adds configuration options for the `WhatsApp` channel to the field widget.
   *
   * Allows the user to set a default message text.
   */
  public static function optionsWidget(array &$element, array $options) {
    $title = static::title();
    $element['text'] = [
      '#title' => t('WhatsApp text for @title.', ['@title' => $title]),
      '#description' => t('WhatsApp text for @title.', ['@title' => $title]),
      '#type' => 'textarea',
      '#cols' => 60,
      '#rows' => 2,
      '#attributes' => [],
      '#default_value' => $options['text'],
    ];
  }

  /**
   * Returns a link field containing a `whatsapp://send` URL.
   *
   * @return array
   *   The link field's renderable array.
   */
  public function render() {
    $data = $this->generateTokenData('whatsapp_share');
    $text = token_replace($this->options['text'], $data);

    return [
      'title' => $this->title(),
      'href' => 'whatsapp://send',
      'query' => [
        'text' => $text,
      ],
      'attributes' => [
        'title' => t('Share this via WhatsApp!'),
        'data-share' => 'whatsapp',
        'class' => ['mobile'],
      ],
      'external' => TRUE,
    ];
  }

}
