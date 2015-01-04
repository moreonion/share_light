<?php

namespace Drupal\share_light;

interface ChannelInterface {
  public static function title();
  public function render($url, $options, $link);
}
