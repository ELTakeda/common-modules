<?php

namespace Drupal\openid_connect;

use Drupal\Component\Utility\Crypt;

/**
 * Creates and validates state tokens.
 *
 * @package Drupal\openid_connect
 */
class OpenIDConnectStateToken implements OpenIDConnectStateTokenInterface {

  /**
   * {@inheritdoc}
   */
  public static function create() {
    $session = \Drupal::request()->getSession();
    $openid_connect_state = $session->get('openid_connect_state');
    if($openid_connect_state)
    {
      return $openid_connect_state;
    }
    else
    {
      $state = Crypt::randomBytesBase64();
      $session->set('openid_connect_state', $state);
      $session->save(); 

      return $state;     
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function confirm($state_token) {
    $session = \Drupal::request()->getSession();
    $openid_connect_state = $session->get('openid_connect_state');
      if($state_token == $openid_connect_state)
      {
        return true;        
      }
      else
      {
        return false;
      }
  }
}
