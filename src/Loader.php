<?php

namespace Drupal\share_light;

class Loader {
  protected static $instance = NULL;
  public static function instance() {
    if (!static::$instance) {
      static::$instance = new static();
    }
    return static::$instance;
  }

  protected $channels;
  public function __construct() {
    $this->channels = module_invoke_all('share_light_channel_info');
  }

  public function allChannels() {
    return $this->channels;
  }

  public function channel($name) {
    $class = $this->channels[$name];
    return new $class();
  }

  public function channelOptions() {
    $options = array();
    foreach ($this->channels as $name => $class) {
      $options[$name] = $class::title();
    }
    return $options;
  }
}
