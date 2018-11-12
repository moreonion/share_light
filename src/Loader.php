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
  protected $channelStatus;

  public function __construct() {
    $this->channels = module_invoke_all('share_light_channel_info');
    $all_enabled = array_fill_keys(array_keys($this->channels), 1);
    $this->channelStatus = variable_get('share_light_channels_enabled', []) + $all_enabled;
    $this->channelStatus = array_intersect_key($this->channelStatus, $this->channels);
  }

  public function allChannels() {
    return $this->channels;
  }

  public function channelClass($name) {
    return $this->channels[$name];
  }

  public function channel($name, $block, $options) {
    $class = $this->channels[$name];
    return new $class($block, $options);
  }

  /**
   * Get an array of channels sorted as in the admin interface.
   *
   * @return array
   *   An associative array keyed by channel IDs. The value is not empty if the
   *   channel is enabled.
   */
  public function channelsEnabled() {
    return $this->channelStatus;
  }

  /**
   * Get an options-array for all available channels.
   *
   * @return string[]
   *   An associative array linking channel names to channel titles.
   */
  public function allChannelOptions() {
    $options = [];
    foreach ($this->channels as $name => $class) {
      $options[$name] = $class::title();
    }
    return $options;
  }

  /**
   * Get an options-array for all enabled channels.
   *
   * @return string[]
   *   An associative array linking channel names to channel titles.
   */
  public function channelOptions() {
    $options = [];
    foreach ($this->channels as $name => $class) {
      if ($this->channelStatus[$name]) {
        $options[$name] = $class::title();
      }
    }
    return $options;
  }

  public function defaults() {
    $defaults = array();
    foreach ($this->channels as $name => $class) {
      $defaults[$name] = array('toggle' => 1) + $class::defaults();
    }
    return $defaults;
  }
}
