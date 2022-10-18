<?php

namespace Drupal\share_light;

use Upal\DrupalUnitTestCase;

/**
 * Test the twitter share link.
 */
class TwitterTest extends DrupalUnitTestCase {

  /**
   * Create a mock block.
   */
  protected function mockBlock() {
    $block = $this->createMock(Block::class);
    $node = (object) [
      'nid' => 'mock-nid',
    ];
    $block->method('getShareNode')->willReturn($node);
    $block->method('getLink')->willReturn([
      'path' => 'node/mock-nid',
    ]);
    return $block;
  }

  /**
   * Test rendering the link with node.
   */
  public function testRenderLink() {
    $channel = new Twitter($this->mockBlock(), [
      'text' => 'Share text.',
    ]);
    $link = $channel->render();
    $text = $link['query']['text'];
    $url = $link['query']['url'];
    $this->assertNotEmpty($url);
    $this->assertStringContainsString('node/mock-nid', $url);
    $this->assertStringNotContainsString($url, $text);
    $this->assertEqual('Share text.', $text);
  }

  /**
   * Test rendering the link with node when [share:url] is part of the text.
   */
  public function testRenderWithShareToken() {
    $channel = new Twitter($this->mockBlock(), [
      'text' => 'Text with [share:url].',
    ]);
    $link = $channel->render();
    $text = $link['query']['text'];
    $url = $link['query']['url'];
    $this->assertEmpty($url);
  }

}
