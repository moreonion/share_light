<?php

namespace Drupal\share_light;

use Upal\DrupalUnitTestCase;

/**
 * Test the WhatsApp share link.
 */
class WhatsAppTest extends DrupalUnitTestCase {

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
   * Test that the share URL is appended to the text if the token is missing.
   */
  public function testShareUrlGetsAppended() {
    $channel = new WhatsApp($this->mockBlock(), [
      'text' => 'Share text.',
    ]);
    $link = $channel->render();
    $text = $link['query']['text'];
    $this->assertContains('node/mock-nid', $text);
  }

  /**
   * Test rendering the link with node when [share:url] is part of the text.
   */
  public function testRenderWithShareToken() {
    $channel = new WhatsApp($this->mockBlock(), [
      'text' => 'Text with [share:url].',
    ]);
    $link = $channel->render();
    $text = $link['query']['text'];
    $this->assertContains('node/mock-nid', $text);
    $this->assertEqual('.', substr($text, -1));
  }

}
