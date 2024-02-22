<?php

namespace Drupal\takeda_id\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Drupal\user\UserInterface;


/**
 * Wraps a Takeda ID link event for event listeners.
 */
class TakedaIdLinkEvent extends Event {

    const EVENT_NAME = 'takeda_id_link';

    /**
     * The Drupal user account.
     *
     * @var \Drupal\user\UserInterface
     */
    public $account;

    /**
     * Construct the object.
     *
     * @param \Drupal\user\UserInterface $account
     *   The account of the user logged in.
     */
    public function __construct(UserInterface $account)
    {
        $this->account = $account;
    }
}
