<?php

namespace Drupal\share_light;

interface ChannelInterface {
  public function __construct(Block $block, array $options);
  public static function title();
  public static function defaults();
  public static function optionsWidget(array &$element, array $options);
  public function enabled();

  /**
   * Return a links-array.
   *
   * The array should be suitable to be used in @see theme_links().
   */
  public function render();
}
