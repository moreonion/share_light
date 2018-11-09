<?php

namespace Drupal\share_light;

/**
 * Provides a block containing share buttons for different social networks.
 */
class Block {

  /**
   * Provides the block's defaults.
   *
   * @return array
   *   An array containing the block's default values for `subject`,
   *   `link` and `node`. As well as the defaults provided by each
   *   social media channel.
   */
  public static function defaults() {
    $default = array(
      'subject' => t('Share this page!'),
      'link' => array('path' => ''),
      'node' => NULL,
      'channels' => Loader::instance()->defaults(),
    );
    return $default;
  }

  protected $options;

  /**
   * Constructor function.
   *
   * Sets the Block instance's `options` to `$options` merged with the block's
   * defaults and sets the Block's `link` path.
   *
   * @param array $options
   *   An array of options for the Block.
   */
  public function __construct(array $options = array()) {
    if (empty($options['subject'])) {
      unset($options['subject']);
    }
    $this->options = drupal_array_merge_deep(static::defaults(), $options);
    drupal_alter('share_light_options', $this->options);

    // Overrides based on the current page / share $_GET-parameter.
    if (empty($this->options['link']['path'])) {
      $this->options['link']['path'] = !empty($_GET['share']) ? $_GET['share'] : current_path();
    }
    if (!($this->options['share_node'] = menu_get_object('node', 1, $this->options['link']['path']))) {
      $this->options['share_node'] = $this->options['node'];
    }
  }

  /**
   * Generates the links for all enabled channels.
   *
   * @return array
   *   An array indexed by channel names containing the arrays generated by
   *   the channels `render()` functions.
   */
  public function channelLinks() {
    $links = array();
    $options = $this->options['channels'];
    $loader = Loader::instance();
    $enabled_channels = array_keys(array_filter($loader->channelsEnabled()));
    foreach ($enabled_channels as $channel_name) {
      $channel_options = isset($options[$channel_name]) ? $options[$channel_name] : array();
      $channel = $loader->channel($channel_name, $this, $channel_options);
      if ($channel->enabled() && ($channel_link = $channel->render())) {
        $links[$channel_name] = $channel_link;
      }
    }
    return $links;
  }

  /**
   * Returns the absolute URL of the link that shall be shared.
   */
  public function getUrl($absolute = TRUE) {
    $link = $this->options['link'];
    return url($link['path'], $link + array('absolute' => $absolute));
  }

  /**
   * Returns the relative URL of the link that shall be shared.
   *
   * @return array
   *   `path`: An internal path or external URL.
   */
  public function getLink() {
    return $this->options['link'];
  }

  /**
   * Returns the containing node's object.
   *
   * This is the node containing the `share_light` block.
   */
  public function getNode() {
    return $this->options['node'];
  }

  /**
   * Returns the node object of the node that shall be shared.
   *
   * This is the node that is used for e.g. generating the share URL.
   */
  public function getShareNode() {
    return $this->options['share_node'];
  }

  /**
   * Returns the URL pointing to the share image.
   *
   * @return string|bool
   *   If an image is specified in the block's options, the URL pointing
   *   to the image's source is returned.
   *   If no image is specified `FALSE` is returned.
   */
  public function getImageUrl() {
    if (!empty($this->options['image']->type)) {
      $img = $this->options['image'];
      if ($img->type == 'image' && ($image = image_load($img->uri))) {
        return file_create_url($image->source);
      }
    }
    return FALSE;
  }

  /**
   * Renders the block into the block datastructure.
   *
   * If google analytics tracking is enabled, the corresponding JavaScript files
   * are added.
   *
   * @see hook_block_view()
   */
  public function render() {
    // Add tracking for GA if googleanalytics module is enabled
    // and share tracking is enabled (default: enabled)
    $tracking_enabled = module_exists('googleanalytics') && variable_get('share_light_tracking_enabled', 1);
    if ($tracking_enabled) {
      drupal_add_js(drupal_get_path('module', 'share_light') . '/tracking.js');
      drupal_add_js(array(
        'share_light' => array(
          'share_url' => $this->getUrl(FALSE),
        ),
      ), 'setting');
    }

    $links = $this->channelLinks();
    foreach ($links as &$x) {
      $x['title'] = "<span>{$x['title']}</span>";
      $x['html'] = TRUE;
    }

    $block['subject'] = $this->options['subject'];
    $block['content'] = array(
      '#theme' => 'links',
      '#links' => $links,
      '#attributes' => array('class' => array('share-light')),
      '#attached' => ['js' => [drupal_get_path('module', 'share_light') . '/hide_mobile_buttons.js']],
    );

    return $block;
  }

  /**
   * Creates a new Block instance based on the node inferred from current path.
   *
   * @param array $options
   *   Additional options for the block.
   *
   * @return Block|null
   *   Returns a new Block if the `share light` block is activated
   *   on the node's `share light` configuration, `NULL` otherwise.
   */
  public static function fromCurrentPath(array $options = array()) {
    $node = NULL;
    if (!($node = menu_get_object())) {
      $count = 0;
      // Replace e.g. "node/306" with "306".
      $nid = preg_replace('/^.*\/(\d+)$/', '$1', current_path(), -1, $count);
      if ($count) {
        $node = node_load($nid);
      }
    }
    if ($node) {
      $options['node'] = $node;
      if ($item = self::configByNode($node, 'share_light')) {
        if ($item['toggle'] == '0') {
          return NULL;
        }
        $options += $item['options'];
      }
    }
    return new static($options);
  }

  /**
   * Retrieve `share_light` data of the given `$node`.
   */
  private static function configByNode($node) {
    $instances = field_info_instances('node', $node->type);
    foreach ($instances as $instance) {
      $field_info = field_info_field($instance['field_name']);
      if ($field_info['type'] == 'share_light') {
        $item = field_get_items('node', $node, $instance['field_name']);
        $data = array();
        if ($item) {
          $data += $item[0];
        }
        if (empty($data['options']['subject'])) {
          unset($data['options']['subject']);
        }
        if (count($instance['default_value'])) {
          $data += $instance['default_value'][0];
        }
        if (empty($data['options']['subject'])) {
          unset($data['options']['subject']);
        }
        return $data;
      }
    }
    return array();
  }

}
