<?php

namespace Drupal\share_light;

class Email extends ChannelBase {
  public static function title() { return t('Email'); }
  public function render() {
    $link = $this->block->getLink();
    $query['path'] = $link['path'];
    if (isset($link['query'])) {
      $query['query'] = $link['query'];
    }
    $parts = explode('/', $query['path']);
    if (count($parts) == 2 && $parts[0] == 'node' && is_numeric($parts[1])) {
      unset($query['path']);
      return array(
        'title' => 'E-Mail',
        'href' => $link['path'] . '/share',
        'query' => $query,
        'attributes' => array(
          'title' => t('Share this via E-Mail!'),
          'data-share' => 'email',
        ),
      );
    }
  }
}
