<?php

namespace Drupal\takeda_datalayer\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class TakedaDatalayerIdController
 * @package Drupal\mymodule\Controller
 */
class TakedaDatalayerIdController
{
    /**
   * @return JsonResponse
   */

    public function index()
    {
        \Drupal::service('page_cache_kill_switch')->trigger();
        return new JsonResponse($this->getData());
    }

    /**
     * @return array
     */

    public function getData()
    {
        $result = [
            'hasSession' => false,
            'digitalId' => null,
            'customerId' => null,
            'gtm' => \Drupal::moduleHandler()->moduleExists('google_tag'),
            'mtm' => \Drupal::moduleHandler()->moduleExists('matomo')
        ];

        if (\Drupal::currentUser()) {
            $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

            /** @var UserDataInterface $userData */
            $userData = \Drupal::service('user.data');
            $digitalId = $userData->get('takeda_id', $user->id(), 'digital_id');
            $customerId = $userData->get('takeda_id', $user->id(), 'customer_id');
        }

        if ($digitalId) {
            $result['hasSession'] = true;
            $result['digitalId'] = $digitalId;
            $result['customerId'] = $customerId;
        }

        return $result;
    }
}
