<?php

namespace Drupal\share_light;

/**
 * A base class for social media channels.
 */
abstract class ChannelBase implements ChannelInterface {
  protected $block;
  protected $options;

  /**
   * Constructor function.
   *
   * @param Block $block
   *   The corresponding `share_light` `Block`.
   * @param array $options
   *   An array of default values.
   */
  public function __construct(Block $block, array $options = array()) {
    $this->block = $block;
    $this->options = $options + static::defaults();
  }

  /**
   * Returns the default values for the channel.
   */
  public static function defaults() {
    return array('toggle' => 1);
  }

  /**
   * Adds a widget containing the options for the Channel.
   *
   * Has to be implemented by the inheriting class.
   *
   * @return array
   *   An empty array.
   */
  public static function optionsWidget(array &$element, array $options) {
    return array();
  }

  /**
   * Returns whether the Channel is enabled or not.
   */
  public function enabled() {
    return !empty($this->options['toggle']);
  }
}
