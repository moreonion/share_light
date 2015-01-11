<?php

namespace Drupal\share_light;

class Email extends ChannelBase {
  public static function title() { return t('Email'); }
  public function render() {
    $node = $this->block->getNode();
    $link = $this->block->getLink();
    $query['path'] = $link['path'];
    if (isset($link['query'])) {
      $query['query'] = $link['query'];
    }
    $parts = explode('/', $query['path']);
    if ($node) {
      if ($query['path'] == 'node/' . $node->nid) {
        unset($query['path']);
      }
      $query['hash'] = static::signQuery($query);
      return array(
        'title' => 'E-Mail',
        'href' => 'node/' . $node->nid . '/share',
        'query' => $query,
        'attributes' => array(
          'title' => t('Share this via E-Mail!'),
          'data-share' => 'email',
        ),
      );
    }
  }
  public static function signQuery($query) {
    $key = drupal_get_hash_salt() . __FILE__;
    return drupal_hmac_base64(serialize($query), $key);
  }
}
