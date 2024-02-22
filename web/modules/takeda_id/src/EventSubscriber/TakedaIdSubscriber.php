<?php

namespace Drupal\takeda_id\EventSubscriber;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Drupal\takeda_id\Controller\TakedaIdController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class TakedaIdSubscriber implements EventSubscriberInterface
{
    /**
     * Class EntityTypeSubscriber.
     *
     * @package Drupal\takeda_id\EventSubscriber
     */
    public static function getSubscribedEvents()
    {
        /**
         * {@inheritdoc}
         *
         * @return array
         *   The event names to listen for, and the methods that should be executed.
         */
        $events[KernelEvents::REQUEST][] = array('takeda_id_init');
        return $events;
    }

    public function takeda_id_init(RequestEvent $event)
    {
        // Init TakedaIdSubscriber
    }
}