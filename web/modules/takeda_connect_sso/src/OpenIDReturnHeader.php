<?php

namespace Drupal\openid_connect;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class OpenIDReturnHeader implements EventSubscriberInterface {

  public function onRespond(FilterResponseEvent $event) {
      $response = $event->getResponse();
      $statusCode = $response->getStatusCode();

      if ($statusCode == "200" && array_key_exists("openid_connect", $_SESSION)) {
          $headersSent = $_SESSION["openid_connect"]["headers_sent"];

          if ($headersSent != "true") {
              $id_token = $_SESSION["openid_connect"]["tokens"]["id_token"];
              $response->headers->set('IDToken', $id_token);

              $access_token = $_SESSION["openid_connect"]["tokens"]["access_token"];
              $response->headers->set('AccessToken', $access_token);

              $refresh_token = $_SESSION["openid_connect"]["tokens"]["refresh_token"];
              $response->headers->set('RefreshToken', $refresh_token);

              $_SESSION["openid_connect"]["headers_sent"] = "true";
          }

      }


  }

  public static function getSubscribedEvents() {
      $events[KernelEvents::RESPONSE][] = array('onRespond');
      return $events;
  }

}

?>
