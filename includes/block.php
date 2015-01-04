<?php

/**
 * @file
 * Block related functions.
 */

use \Drupal\share_light\Loader;

/**
 * Implements hook_block_info().
 */
function share_light_block_info() {
  $blocks = array();

  $blocks['current_page'] = array(
    'info' => t('Share this page'),
    'cache' => DRUPAL_CACHE_PER_PAGE,
  );
  return $blocks;
}

/**
 * Implements hook_block_view().
 * @param string $path to the page - current url will be used if empty
 * @param array $options additional overrides for default options
 * @return renderable array
 */
function share_light_block_view($id, $link = NULL, $options = array()) {
  // Load options from the currently displayed node.
  $node = NULL;
  if (!($node = menu_get_object())) {
    $count = 0;
    $nid = preg_replace('/^.*\/(\d+)$/', '$1', $link, -1, $count);
    if ($count) {
      $node = node_load($nid);
    }
  }
  if ($node) {
    if ($item = _share_light_field_config_by_node($node, 'share_light')) {
      if ($item['toggle'] == '0') {
        return NULL;
      }
      if (empty($item['options']['subject'])) {
        unset($item['options']['subject']);
      }
      $options += $item['options'];
    }
  }

  // overrides based on the current page / shared page.
  if (empty($options['share_url']) && isset($_GET['share'])) {
    $options['share_url'] = $_GET['share'];
  }

  if (empty($options['share_url'])) {
    $options['share_url'] = current_path();
  }

  // add tracking for GA if googleanalytics module is enabled
  // and share tracking is enabled (default: enabled)
  $tracking_enabled = module_exists('googleanalytics') && variable_get('share_light_tracking_enabled', '1') == '1';
  if ($tracking_enabled) {
    drupal_add_js(drupal_get_path('module', 'share_light') . '/tracking.js');
    drupal_add_js(array('share_light' => array(
      'share_url' => $options['share_url'],
    )), 'setting');
  }


  if ($link) {
    if (!is_array($link)) {
      $link = array('path' => $link);
    }
    $options['share_url'] = $link['path'];
  }

  // Global defaults
  $defaults = _share_light_defaults();
  $options += $defaults['options'];

  drupal_alter('share_light_options', $options);

  $link['path'] = $options['share_url'];
  $url = url($link['path'], $link + array('absolute' => TRUE));

  // try to use a default image url if no image url is saved (yet)
  $image_default_fields = variable_get('share_light_image_default_fields', array());
  if (!is_array($image_default_fields)) {
    $image_default_fields = array($image_default_fields);
  }
  // an fid of 0 gets saved if no image is selected
  // @TODO deal with the case somebody explicitly does not want to
  // share a default field
  if ((empty($options['image']) || $options['image']['fid'] == 0) && !empty($node->nid)) {
    foreach ($image_default_fields as $try_field) {
      if ($found_image = field_get_items('node', $node, $try_field)) {
        $options['image']['fid'] = $found_image[0]['fid'];
      }
    }
  }

  $options['image'] = file_load($options['image']['fid']);

  $links = array();
  // display the enabled channels
  $channels = variable_get('share_light_channels_enabled', Loader::instance()->channelOptions());
  foreach ($channels as $channel_name => $channel_value) {
    if ($channel_value) {
      if (!isset($options['advanced']['channel_'.$channel_name.'_toggle']) ||
        $options['advanced']['channel_'.$channel_name.'_toggle'] == 1) {
        $channel = Loader::instance()->channel($channel_name);
        if($channel_link = $channel->render($url, $options, $link)) {
          $links[$channel_name] = $channel_link;
        }
      }
    }
  }

  foreach ($links as &$x) {
    $x['title'] = "<span>{$x['title']}</span>";
    $x['html'] = TRUE;
    $x['attributes']['target'] = '_blank';
  }

  $v['subject'] = $options['subject'];
  $v['content'] = array(
    '#theme' => 'links',
    '#links' => $links,
    '#attributes' => array('class' => array('share-light')),
  );
  return $v;
}
