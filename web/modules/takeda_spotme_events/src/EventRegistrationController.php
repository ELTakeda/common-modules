<?php

namespace Drupal\spotme_events;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides route responses for the Example module.
 */
class EventRegistrationController extends ControllerBase{
    public function registerUserForEventCallback() {
        $data = $_POST;

        // This field has to be present in the HTML and content fed from the event page
        $eventId = $data['eventId'] ? $data['eventId'] : '';
        $eventType = $data['eventType'] ? $data['eventType'] : ''; 

        // Get the user data from the site
        $user = \Drupal::currentUser();
        $user_id = $user->id();

        // Get the other user data from his session       
        $email = $user->getEmail();
        $takeda_id = \Drupal::service('spotme_events.custom_functions')->getFieldDataFromDB($user_id, 'user__field_takeda_id', 'field_takeda_id_value');

        $takeda_user_info = \Drupal::service('spotme_events.curl_functions')->getUserDigitalId($takeda_id);

        $customer_id = $takeda_user_info->profile->customer_id;
        $user_country_code =  $takeda_user_info->profile->crm_country;

        // Prepare the data
        $data = [
            'email' => $email,
            'takedaId' => $customer_id,
            'eventId' => $eventId,
            'event_type' => $eventType,
            'country_code' => 'BG',
        ];

        if($eventType == 'spotme' || $eventType == 'teams'){
           \Drupal::service('spotme_events.curl_functions')->registerVeevaUser($eventId, $customer_id, $user_country_code);
        }

        // Save the data to the module table
        $isSavedToDB = \Drupal::service('spotme_events.custom_functions')->saveUserDataToDatabase($data, $user_id);        

        // After the data is saved
        if ($isSavedToDB) {
            // Return succes status to the user
            // for FE feedback
            return new JsonResponse(
                [
                'status' => 'success', 
                Response::HTTP_OK
                ]
            );
        }

        // Give feedback if the user is already registered for the event
        return new JsonResponse(
            [
            'status' => 'exists', 
            Response::HTTP_OK
            ]
        );
    }
}