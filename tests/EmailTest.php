<?php

/**
 * @file
 *
 * Tests for the share-light module.
 */

use \Drupal\share_light\Block;
use \Drupal\share_light\Email;

/**
 * Test the email channel.
 */
class EmailWebTest extends DrupalWebTestCase {

  public static function getInfo() {
    return [
      'name' => 'Email channel',
      'description' => 'Test the email channel class.',
      'group' => 'Share light',
    ];
  }

  public function setUp() {
    parent::setUp(['share_light', 'node']);
    // Create Basic page and Article node types.
    if ($this->profile != 'standard') {
      $this->drupalCreateContentType(array('type' => 'page', 'name' => 'Basic page'));
      $this->drupalCreateContentType(array('type' => 'article', 'name' => 'Article'));
    }
  }

  public function testRenderWithNonNodePath() {
    $node = $this->drupalCreateNode(array('type' => 'page'));
    $block = new Block(['node' => $node, 'link' => ['path' => 'path']]);
    $email = new Email($block);
    $render = $email->render();
    $this->assertEqual("node/{$node->nid}/share", $render['href']);
    $this->assertEqual('path', $render['query']['path']);
  }

  public function testRenderWithNodePath() {
    $node = $this->drupalCreateNode(array('type' => 'page'));
    $block = new Block(['node' => $node, 'link' => ['path' => "node/{$node->nid}"]]);
    $email = new Email($block);
    $render = $email->render();
    $this->assertEqual("node/{$node->nid}/share", $render['href']);
    $this->assertEqual([], $render['query']);

    $node2 = $this->drupalCreateNode(array('type' => 'page'));
    $block = new Block(['node' => $node, 'link' => ['path' => "node/{$node2->nid}"]]);
    $email = new Email($block);
    $render = $email->render();
    $this->assertEqual("node/{$node2->nid}/share", $render['href']);
    $this->assertEqual([], $render['query']);
    $this->assertEqual($path, $render['query']['path']);
  }

}
