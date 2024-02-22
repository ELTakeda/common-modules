<?php

namespace Drupal\spotme_events;

class CustomFunctions {

    public function getProtectedValue($obj, $name){
        $array = (array)$obj;
        $prefix = chr(0).'*'.chr(0);
        return $array[$prefix.$name];
    }
    
    public function getValueFromArray($arr){
        if(!empty($arr)){
            $value = $arr[0]['value'];
        }else{
            $value = '';
        }
        return $value;
    }

    public function getFieldDataFromDB($user_id, $table, $column) {
        $database = \Drupal::database();

        $field_data = $database->select($table, 'f')
            ->fields('f', [$column])
            ->condition('entity_id', $user_id)
            ->execute()
            ->fetchField();

        return $field_data;
    }

    public function saveUserDataToDatabase($data, $user_id_param) {
        // Get the fields from the data
        $email = $data['email'];
        $event_id = $data['eventId'];
        $created_on = date('Y-m-d H:i:s',time());
        $event_type = $data['event_type'];
        $country_code = $data['country_code'];
        $user_id = $user_id_param;
        $takeda_id = $data['takedaId'];

        // Check if the user email is singed up for that event
        $database = \Drupal::database();
        $table = 'event_registration';

        $email_exists = $database->select($table, 'f')
            ->condition('event_id', $event_id)
            ->condition('email', $email)
            ->condition('created_on', $created_on)
            ->countQuery()
            ->execute()
            ->fetchField();

        // If any results are returned, the user is already signed up
        if ($email_exists) {
            // Return that the user already exists
            return false;
        }
        
        // If the user doesn't exist, populate the table
        $database->insert($table)
            ->fields([
                'email' => $email,
                'event_id' => $event_id,
                'user_id' => $user_id,
                'takeda_id' => $takeda_id,
                'event_type' => $event_type,
                'country_code' => $country_code,
                'spotme_url' => '',
                'is_sent' => 0,
            ])
            ->execute();

        return true;
    }
}