<?php

namespace Drupal\openid_connect;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterReponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RedirectAfterLogoutSubscriber implements EventSubscriberInterface {

    public function checkRedirection(ResponseEvent $event) {
        $destination = &drupal_static('openid_connect_user_logout');

        $response = $event->getResponse();
        if (!$response instanceof RedirectResponse || !$destination) {
            return;
        }

        $response = new RedirectResponse($destination);
        $response->send();
    }

    public static function getSubscribedEvents() {
        $events[KernelEvents::RESPONSE][] = ['checkRedirection'];
        return $events;
    }
}
