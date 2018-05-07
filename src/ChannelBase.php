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
   * Generate form for editing the channel specific settings.
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

  /**
   * Generates the URL to be shared.
   *
   * @param string $utm_source
   *   The `utm_source` query parameter's value (e.g. `twitter_share`).
   * @param object|null $node
   *   The `node` object containing the `node`'s `nid`. If `NULL` is
   *   provided, the node will be fetched via `getNodeAndQuery()`.
   *
   * @return string
   *   The generated URL or the value of the block's `getUrl` if no node
   *   could be fetched.
   */
  protected function generateShareUrl($utm_source, $node = NULL) {
    if (!$node) {
      $node = $this->block->getShareNode();
    }

    $url = $this->block->getUrl();
    if ($node) {
      $url .= strpos($url, '?') ? '&' : '?';
      $url .=
        'utm_source=' . $utm_source . '&utm_campaign=[' .
        $node->nid . ']&utm_medium=share';

      return $url;
    }

    return $url;
  }

  /**
   * Generates data needed for token replacement.
   *
   * @param string $utm_source
   *   The `utm_source` query parameter's value for `[share:url]`
   *   (e.g. `twitter_share`).
   *
   * @return array
   *   The token replacement data, containing the corresponding `node`
   *   and `share` data. The `node` can be `NULL`.
   */
  protected function generateTokenData($utm_source) {
    $node = $this->block->getShareNode();
    $url = $this->generateShareUrl($utm_source, $node);

    return ['node' => $node, 'share' => ['url' => $url]];
  }

}
