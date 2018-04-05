<?php

namespace Drupal\share_light;

/**
 * A channel for sharing via e-mail.
 */
class Email extends ChannelBase {

  /**
   * Returns the channel's name.
   *
   * @return string
   *   Returns t('Email').
   */
  public static function title() {
    return t('Email');
  }

  /**
   * Renders the HTML element.
   */
  public function render() {
    $node = $this->block->getNode();
    $link = $this->block->getLink();
    $query['path'] = $link['path'];
    if (isset($link['query'])) {
      $query['query'] = $link['query'];
    }
    if ($p_node = menu_get_object('node', 1, $query['path'])) {
      $node = $p_node;
    }
    if ($node) {
      if ($query['path'] == 'node/' . $node->nid) {
        unset($query['path']);
      }
      if ($query) {
        $query['hash'] = static::signQuery($query);
      }
      return array(
        'title' => 'Email',
        'href' => 'node/' . $node->nid . '/share',
        'query' => $query,
        'attributes' => array(
          'title' => t('Share this via email!'),
          'data-share' => 'email',
          'target' => '_blank',
        ),
      );
    }
  }

  /**
   * Generates a base64 encoded HMAC.
   *
   * @return string
   *   A base64 encoded HMAC with the salted filepath
   *   as secret key.
   */
  public static function signQuery($query) {
    $key = drupal_get_hash_salt() . __FILE__;
    return drupal_hmac_base64(serialize($query), $key);
  }
}
