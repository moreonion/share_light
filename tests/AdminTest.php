<?php

namespace Drupal\share_light;

/**
 * Test the admin forms.
 */
class AdminTest extends \DrupalUnitTestCase {

  /**
   * Test the general block settings form.
   */
  public function testBlockSettingsForm() {
    $GLOBALS['user'] = user_load(1);
    $form = menu_execute_active_handler('admin/config/content/share-light', FALSE);
    $checkboxes = $form['share_light_channels_enabled']['enabled'];
    // By default all channels are enabled.
    $this->assertEqual($checkboxes['#options'], array_filter($checkboxes['#options']));
  }

}
