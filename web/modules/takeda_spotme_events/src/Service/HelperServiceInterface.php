<?php

namespace Drupal\spotme_events\Service;

/**
 * Interface HelperServiceInterface.
 */
interface HelperServiceInterface {

  /**
   * {@inheritdoc }
   */
  public function postEvent($data);

  /**
   * {@inheritdoc }
   */
  public function getLinkEvent($data);

  /**
   * {@inheritdoc }
   */
  public function registerEvent($data);

  /**
   * {@inheritdoc }
   */
  public function getMachineName($string);

}
