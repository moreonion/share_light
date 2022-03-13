<?php

/**
 * @file
 * Tests for the share-light module.
 */

use Drupal\share_light\Block;
use Drupal\share_light\Email;

/**
 * Test the email channel.
 */
class EmailWebTest extends DrupalWebTestCase {

  /**
   * Provides information on the `EmailWebTest` testcase to `SimpleTest`.
   */
  public static function getInfo() {
    return [
      'name' => 'Email channel',
      'description' => 'Test the email channel class.',
      'group' => 'Share light',
    ];
  }

  /**
   * Sets up content types for the tests if not available.
   */
  public function setUp(): void {
    parent::setUp(['share_light', 'node']);

    if ($this->profile != 'standard') {
      $this->drupalCreateContentType(array('type' => 'page', 'name' => 'Basic page'));
      $this->drupalCreateContentType(array('type' => 'article', 'name' => 'Article'));
    }
  }

  /**
   * If the share path doesn't point to a node use the current node.
   */
  public function testRenderWithNonNodePath() {
    $node = $this->drupalCreateNode(array('type' => 'page'));
    $block = new Block(['node' => $node, 'link' => ['path' => 'path']]);
    $email = new Email($block);
    $render = $email->render();
    $this->assertEqual("node/{$node->nid}/share", $render['href']);
    $this->assertEqual('path', $render['query']['path']);
  }

  /**
   * Node paths in the block configuration should override the current node.
   */
  public function testRenderWithNodePath() {
    // Share path points to the same node.
    $node = $this->drupalCreateNode(array('type' => 'page'));
    $path = "node/{$node->nid}";
    $block = new Block(['node' => $node, 'link' => ['path' => $path]]);
    $email = new Email($block);
    $render = $email->render();
    $this->assertEqual("node/{$node->nid}/share", $render['href']);
    // The share path points to the share node. No need to pass the path.
    $this->assertEqual([], $render['query']);

    // Share path points to another node.
    $node2 = $this->drupalCreateNode(array('type' => 'page'));
    $path = "node/{$node2->nid}";
    $block = new Block(['node' => $node, 'link' => ['path' => $path]]);
    $email = new Email($block);
    $render = $email->render();
    $this->assertEqual("node/{$node2->nid}/share", $render['href']);
    // The share path points to the share node. No need to pass the path.
    $this->assertEqual([], $render['query']);

    // Change node even it the path points to some sub-folder.
    $path = "node/{$node2->nid}/something/else";
    $block = new Block(['node' => $node, 'link' => ['path' => $path]]);
    $email = new Email($block);
    $render = $email->render();
    $this->assertEqual("node/{$node2->nid}/share", $render['href']);
    // Path should be left untouched.
    $this->assertEqual($path, $render['query']['path']);
  }

}
