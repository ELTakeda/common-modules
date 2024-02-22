<?php
namespace Drupal\spotme_events\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class ReloadPageCommandSpotme implements CommandInterface {

  /**
   * A CSS selector string of the dialog to close.
   *
   * @var string
   */
  protected $selector;

  /**
   * Constructs a CloseDialogCommand object.
   *
   * @param string $selector
   *   A CSS selector string of the dialog to close.
   */
  public function __construct($selector = NULL) {
    $this->selector = $selector ? $selector : '#drupal-modal';
  }

  public function render() {
    return [
      'command' => 'ReloadPageCommandSpotme',
      'selector' => $this->selector,
    ];
  }
}
