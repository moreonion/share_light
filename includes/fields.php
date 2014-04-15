<?php

/**
 * @file
 * Field related functions.
 */

/**
 * Implements hook_field_info().
 */
function share_light_field_info() {
  $info['share_light'] = array(
    'label' => t('Share light'),
    'description' => t('Allows you to display a share block.'),
    'settings' => array('style' => NULL),
    'default_widget' => 'share_light',
    'default_formatter' => 'share_light',
  );
  return $info;
}


/**
 * Implements hook_field_presave().
 */
function share_light_field_presave($entity_type, $entity, $field, $instance, $langcode, &$items) {
  if ($field['type'] == 'share_light') {
    foreach ($items as &$item) {
      $item['options'] = serialize($item['options']);
    }
  }
}

/**
 * Implements hook_field_load().
 */
function share_light_field_load($entity_type, $entities, $field, $instances, $langcode, &$items, $age) {
  if ($field['type'] == 'share_light') {
    foreach ($entities as $id => $entity) {
      foreach ($items[$id] as &$item) {
        $item['options'] = unserialize($item['options']);
      }
    }
  }
}

/**
 * Implements hook_field_is_empty().
 */
function share_light_field_is_empty($item, $field) {
  return FALSE;
}

/**
 * Utility function: returns all field items by field type in array
 * (indexed by field name)
 */
function _share_light_field_config_by_node($node, $field_type) {
  $instances = field_info_instances('node', $node->type);
  foreach ($instances as $instance) {
    $field_info = field_info_field($instance['field_name']);
    if ($field_info['type'] == $field_type) {
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

/**
 * Implements hook_field_widget_info().
 */
function share_light_field_widget_info() {
  $info['share_light'] = array(
    'label' => t('Share light'),
    'field types' => array('share_light'),
    'settings' => array('size' => 60),
    'behaviors' => array(
      'multiple values' => FIELD_BEHAVIOR_DEFAULT,
      'default values' => FIELD_BEHAVIOR_DEFAULT,
    ),
  );
  return $info;
}

/**
 * Implements hook_field_widget_form().
 */
function share_light_field_widget_form(&$form, &$form_state, $field, $instance, $langcode, $items, $delta, $element) {
  $item = isset($items[$delta]) ? $items[$delta] : array();
  if (isset($instance['default_value'][$delta]) && !isset($items[$delta])) {
    $item = $instance['default_value'][$delta];
  }

  $available_channels = _share_light_channels();
  $enabled_channels = variable_get('share_light_channels_enabled', $available_channels);

  $item = drupal_array_merge_deep(_share_light_defaults(), $item);

  $toggle_id = drupal_html_id('share-light-widget-toggle');
  $element['toggle'] = array(
    '#title' => t('Display a share block.'),
    '#description' => t('Display a share block.'),
    '#type' => 'checkbox',
    '#default_value' => $item['toggle'],
    '#attributes' => array('id' => $toggle_id),
  );

  $element['options'] = array(
    '#type' => 'container',
    '#states' => array(
      'visible' => array(
        '#' . $toggle_id => array('checked' => TRUE),
      ),
    ),
  );

  $element['options']['subject'] = array(
    '#title' => t('Title of the share-box.'),
    '#description' => t('The title is typically displayed right above the share buttons.'),
    '#type' => 'textfield',
    '#default_value' => $item['options']['subject'],
  );

  $element['options']['share_url'] = array(
    '#title' => t('URL to be shared.'),
    '#description' => t('URL to be shared. Leave this empty to share the current page.'),
    '#type' => 'textfield',
    '#size' => 60,
    '#default_value' => $item['options']['share_url'],
  );

  $element['options']['image'] = array(
    '#title' => t('Image to be shared.'),
    '#description' => t('Image to be shared.'),
    '#type' => 'media',
    '#default_value' => $item['options']['image'],
    '#access' => FALSE, // TODO
  );

  $element['options']['counter_toggle'] = array(
    '#title' => t('Show counter (number of shares) along with share options'),
    '#description' => t('Show counter (number of shares) along with share options'),
    '#type' => 'checkbox',
    '#default_value' => $item['options']['counter_toggle'],
    '#access' => FALSE, // TODO
);

  $element['options']['advanced'] = array(
    '#type' => 'fieldset',
    '#title' => t('Advanced share options'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $weight = 10;
  foreach ($enabled_channels as $channel_name => $channel_value) {
    if (!isset($available_channels[$channel_name])) {
      continue;
    }

    $title = $available_channels[$channel_name];
    $ctoggle_id = drupal_html_id('share-light-channel-' . $channel_name . '-toggle');
    $element['options']['advanced']['channel_'.$channel_name.'_toggle'] = array(
      '#title' => t('Show '.$title.' share button.'),
      '#description' => t('Enable '.$title.' on this page.'),
      '#type' => 'checkbox',
      '#default_value' => $item['options']['advanced']['channel_'.$channel_name.'_toggle'],
      '#access' => $channel_value ? TRUE : FALSE,
      '#weight' => $weight,
      '#attributes' => array('id' => $ctoggle_id),
    );

    // needs a text field
    if ($channel_name == 'twitter' || $channel_name == 'pinterest') {
      $title = $available_channels[$channel_name];
      $textarea =  array(
        '#title' => t('Share text for ' . $title . '.'),
        '#description' => t('Share text for ' . $title . '.'),
        '#type' => 'textarea',
        '#cols' => 60,
        '#rows' => 2,
        '#maxlength' => 225,
        '#attributes' => array(),
        '#default_value' => $item['options']['advanced']['channel_'.$channel_name.'_text'],
        '#access' => $channel_value ? TRUE : FALSE,
        '#weight' => $element['options']['advanced']['channel_'.$channel_name.'_toggle']['#weight'] + 1,
        '#states' => array(
          'visible' => array(
            '#' . $ctoggle_id => array('checked' => TRUE),
          ),
        ),
      );
      // Twitter
      if ($channel_name == 'twitter') {
        $textarea['#title'] = t('Tweet text for ' . $title . '.');
        $textarea['#description'] = t('Tweet text for ' . $title . '.');
        $textarea['#maxlength'] = 116; // = 140 - 1 - 23 (tweet max-length - space - url in https)

      // Pinterest
      } else if ($channel_name == 'pinterest') {
        $textarea['#title'] = t('Description text for ' . $title . '.');
        $textarea['#description'] = t('Description text for ' . $title . '.');
        $textarea['#maxlength'] = 500; // the pinterest max-length for descriptions
      }

      $element['options']['advanced']['channel_'.$channel_name.'_text'] = $textarea;
    }

    $weight += 10;
  }

  return $element;
}



/**
 * Implements hook_field_formatter_info().
 */
function share_light_field_formatter_info() {
  $info['share_light'] = array(
    'label' => 'Share light',
    'field types' => array('share_light'),
  );
  return $info;
}
