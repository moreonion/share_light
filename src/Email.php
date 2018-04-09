<?php

namespace Drupal\share_light;

/**
 * A channel for sharing via e-mail.
 */
class Email extends ChannelBase {

  /**
   * Returns the channel's name.
   *
   * @return string
   *   Returns t('Email').
   */
  public static function title() {
    return t('Email');
  }

  /**
   * Returns the default values for the channel.
   */
  public static function defaults() {
    return [
      'mailto_toggle' => FALSE,
      'mailto' =>
      [
        'subject' => '[node:title]',
        'body' => '[share:url]',
      ],
    ] + parent::defaults();
  }

  /**
   * Renders the HTML element.
   */
  public function render() {
    if ($this->options['mailto_toggle']) {
      return $this->renderLink();
    }

    return $this->renderForm();
  }

  /**
   * Renders an HTML email form..
   */
  private function renderForm() {
    $nq = $this->getNodeAndQuery();
    $node = $nq['node'];
    $query = $nq['query'];

    if ($node) {
      if ($query) {
        $query['hash'] = static::signQuery($query);
      }
      return array(
        'title' => 'Email',
        'href' => 'node/' . $node->nid . '/share',
        'query' => $query,
        'attributes' => array(
          'title' => t('Share this via email!'),
          'data-share' => 'email',
          'target' => '_blank',
        ),
      );
    }
  }

  /**
   * Returns a link field containing a `mailto:` URI.
   *
   * @return array
   *   The link field's renderable array.
   */
  private function renderLink() {
    $data = $this->generateTokenData('email_share');
    $query['subject'] = htmlspecialchars_decode(
      token_replace($this->options['mailto']['subject'], $data),
      ENT_QUOTES);
    $query['body'] = htmlspecialchars_decode(
      token_replace($this->options['mailto']['body'], $data),
      ENT_QUOTES);

    return [
      'title' => 'Email',
      'href' => 'mailto:',
      'query' => $query,
      'attributes' => [
        'title' => t('Share this via Email!'),
        'data-share' => 'email',
      ],
      'external' => TRUE,
    ];
  }

  /**
   * Generates a base64 encoded HMAC.
   *
   * @return string
   *   A base64 encoded HMAC with the salted filepath
   *   as secret key.
   */
  public static function signQuery($query) {
    $key = drupal_get_hash_salt() . __FILE__;
    return drupal_hmac_base64(serialize($query), $key);
  }

  /**
   * Adds configuration options for the `Email` channel to the field widget.
   *
   * Allows the user to choose between sharing via an online form
   * or the local Email client via a `mailto:` URI.
   */
  public static function optionsWidget(array &$element, array $options) {
    $title = static::title();
    $ctoggle_id = drupal_html_id('share-light-channel-' . $title . '-mailto');
    $element['mailto_toggle'] = [
      '#type' => 'checkbox',
      '#title' => t("Use mail:to link for email sharing via the users' email client"),
      '#description' => t("Enables the user to send an email via the users' local email client instead of a webform"),
      '#attributes' => ['id' => $ctoggle_id],
      '#default_value' => $options['mailto_toggle'],
      '#value_callback' => 'email_mailto_checkbox_value',
    ];
    $element['mailto'] = [
      '#type' => 'container',
      '#states' => ['visible' => ['#' . $ctoggle_id => ['checked' => TRUE]]],
    ];
    $element['mailto']['subject'] = [
      '#title' => t('Subject'),
      '#description' => t("The email's subject line."),
      '#type' => 'textfield',
      '#size' => 60,
      '#default_value' => $options['mailto']['subject'],
    ];
    $element['mailto']['body'] = [
      '#title' => t('Body'),
      '#description' => t("The email's body."),
      '#type' => 'textarea',
      '#cols' => 60,
      '#rows' => 5,
      '#default_value' => $options['mailto']['body'],
    ];
  }

}
