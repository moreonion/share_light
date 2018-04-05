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

  /**
   * Fetches the node object we want to share.
   *
   * In case of `campaigninon_thankyou_page`s, the parent node
   * will be loaded (e.g. the corresponding petition).
   *
   * @return object
   *   An array containing the node object and the URL query.
   */
  protected function getNode() {
    $node = $this->block->getNode();
    $link = $this->block->getLink();
    $query['path'] = $link['path'];
    if (isset($link['query'])) {
      $query['query'] = $link['query'];
    }
    if ($p_node = menu_get_object('node', 1, $query['path'])) {
      $node = $p_node;
    }
    if ($node && $query['path'] == 'node/' . $node->nid) {
      unset($query['path']);
    }

    return array(
      'node' => $node,
      'query' => $query,
    );
  }

  /**
   * Generates the URL to be shared.
   *
   * @param string $utm_source
   *   The `utm_source` query parameter's value (e.g. `twitter_share`).
   * @param object|null $node
   *   The `node` object containing the `node`'s `nid`. If `NULL` is
   *   provided, the node will be fetched via `getNode()`.
   *
   * @return string|null
   *   The generated URL or NULL if no node could be fetched.
   */
  protected function generateShareUrl($utm_source, $node = NULL) {
    if (!$node) {
      $node = $this->getNode()['node'];
    }

    if ($node) {
      $url = $this->block->getUrl();
      $url .= strpos($url, '?') ? '&' : '?';
      $url .=
        'utm_source=' . $utm_source . '&utm_campaign=[' .
        $node->nid . ']&utm_medium=share';

      return $url;
    }
  }

  /**
   * Generates data needed for token replacement.
   *
   * @param string $utm_source
   *   The `utm_source` query parameter's value for `[share:url]`
   *   (e.g. `twitter_share`).
   *
   * @return array|null
   *   The token replacement data, contaning the corresponding `node`
   *   and `share` data.
   *   Or NULL if no node could be fetched.
   */
  protected function generateTokenData($utm_source) {
    $nq = $this->getNode();
    $node = $nq['node'];
    $url = $this->generateShareUrl($utm_source, $node);

    if ($url) {
      return ['node' => $node, 'share' => ['url' => $url]];
    }
  }

}
