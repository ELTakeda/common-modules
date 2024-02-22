<?php

namespace Drupal\takeda_id\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase
{

    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection)
    {

        // if ($route = $collection->get('user.login')) {
        //     // Test set login route
        //     $route->setPath('/login');
        // }

        // Update user password reset page
        $route = $collection->get('user.reset');
        if ($route) {
            $route->setDefaults([
                '_title' => 'Choose a new password',
                '_controller' => '\Drupal\takeda_id\Controller\User::resetPass',
            ]);
            $route->setRequirement(
                '_custom_access',
                '\Drupal\takeda_id\Access\ResetPassAccessCheck::access'
            );
        }
    }
}
