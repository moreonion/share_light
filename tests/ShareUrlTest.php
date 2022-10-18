<?php

namespace Drupal\share_light;

/**
 * Tests whether share URLs are generated correctly for all content types.
 */
class ShareUrlTest extends \DrupalUnitTestCase {

  /**
   * Sets up content types for the tests if not available.
   */
  public function setUp(): void {
    parent::setUp(['share_light', 'node']);

    if ($this->profile != 'standard') {
      $this->drupalCreateContentType(array('type' => 'page', 'name' => 'Basic page'));
    }
  }

  /**
   * Test if share URLs are generate correctly.
   */
  public function testShareUrls() {
    $node = $this->drupalCreateNode(['type' => 'page']);
    $block = new Block(['node' => $node, 'link' => ['path' => '/node/' . $node->nid]]);
    $email = (new Email($block))->render();
    $fb = (new Facebook($block))->render();
    $twitter = (new Twitter($block))->render();
    $fb_msg = (new FacebookMessenger($block))->render();
    $whatsapp = (new WhatsApp($block))->render();
    $mailto = (new Email($block, ['mailto_toggle' => TRUE]))->render();

    $this->assertEquals('node/' . $node->nid . '/share', $email['href']);
    $this->assertEquals('/node/' . $node->nid, $email['query']['path']);
    $this->assertEquals('https://www.facebook.com/sharer.php', $fb['href']);
    $this->assertStringContainsString('node/' . $node->nid, $fb['query']['u']);

    $this->assertEquals('http://twitter.com/share', $twitter['href']);
    $this->assertStringContainsString('node/' . $node->nid, $twitter['query']['text']);
    $this->assertStringContainsString('utm_campaign=%5B' . $node->nid . '%5D', $twitter['query']['text']);

    $this->assertEquals('fb-messenger://share', $fb_msg['href']);
    $this->assertEquals(TRUE, $fb_msg['external']);
    $this->assertStringContainsString('node/' . $node->nid, $fb_msg['query']['link']);

    $this->assertEquals('whatsapp://send', $whatsapp['href']);
    $this->assertEquals(TRUE, $whatsapp['external']);
    $this->assertStringContainsString('node/' . $node->nid, $whatsapp['query']['text']);
    $this->assertStringContainsString('utm_campaign=%5B' . $node->nid . '%5D', $whatsapp['query']['text']);

    $this->assertEquals('mailto:', $mailto['href']);
    $this->assertEquals(TRUE, $mailto['external']);
    $this->assertStringContainsString($node->title, $mailto['query']['subject']);
    $this->assertStringContainsString('node/' . $node->nid, $mailto['query']['body']);
    $this->assertStringContainsString('utm_campaign=%5B' . $node->nid . '%5D', $mailto['query']['body']);
  }

}
