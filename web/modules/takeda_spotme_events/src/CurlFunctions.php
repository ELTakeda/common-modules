<?php

namespace Drupal\spotme_events;

class CurlFunctions {

    public function registerVeevaUser($event_id, $crm_id, $user_country_code){

        //get parameters
        $config = \Drupal::config('event_registration.settings');
        $client_id = $config->get('event_registration_client_id');
        $client_secret = $config->get('event_registration_client_secret');
        $client_env = $config->get('event_registration_environment_veeva_select');
        if(!$client_id || !$client_secret) return [];

        //change URL depending on the link provided
        $takeda_id_endpoint_url = '';
        if($client_env == "dev") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_veeva_dev');
        } else if ($client_env == "sit") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_veeva_sit');
        } else if ($client_env == "uat") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_veeva_uat');
        } else if ($client_env == "prod") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_veeva_prod');
        } else {
            return [];
        }
        if(!$takeda_id_endpoint_url ) return [];
       
        $urlToSend = $takeda_id_endpoint_url;

        $sourceName = \Drupal::request()->getHost();

        $bodyToSend = '[{"activityId":"'.$event_id.'","takedaEnterpriseId":"'.$crm_id.'","attendeeStatus":"Invited","attendeeType":"Account","sourceName":"'.$sourceName.'","countryCode":"'.$user_country_code.'"}]';
    
        $client = \Drupal::httpClient();
        $request = $client->request('POST', $urlToSend, [
            "headers" => [
                "client_id" => $client_id,
                "client_secret" => $client_secret,
                "Content-Type" => "application/json",
            ],
            "body" => $bodyToSend
        ]);
        $response = json_decode($request->getBody())[0];

        return $response;
    }

    public function getSpotmeLiveURL($event_id, $crm_id, $user_country_code){

        // get configuration values
        $config = \Drupal::config('event_registration.settings');
        $client_env = $config->get('event_registration_environment_spotme_url_select');
        $client_id = $config->get('event_registration_client_id');
        $client_secret = $config->get('event_registration_client_secret');
        if(!$client_id || !$client_secret) return [];
        
        //change URL depending on the link provided
        $takeda_id_endpoint_url = '';
        if($client_env == "dev") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_spotme_url_dev');
        } else if ($client_env == "sit") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_spotme_url_sit');
        } else if ($client_env == "uat") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_spotme_url_uat');
        } else if ($client_env == "prod") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_spotme_url_prod');
        } else {
            return [];
        }
        if(!$takeda_id_endpoint_url ) return [];
    
        $urlToSend = $takeda_id_endpoint_url . "?activityId=".$event_id."&takedaEnterpriseId=".$crm_id."&countryCode=".$user_country_code."&attendeeType=Account";
    
        $client = \Drupal::httpClient();
        try{
            $request = $client->request('GET', $urlToSend, [
                "headers" => [
                    "client_id" => $client_id,
                    "client_secret" => $client_secret,
                    "Content-Type" => "application/json",
                ]
            ]);
            
            $response = json_decode($request->getBody());

            return $response;
        }catch(\GuzzleHttp\Exception\RequestException $e){
            $response = $e->getResponse();

            if($response->getStatusCode() >= 300){
                return [];
            }
        }
        
    }

    public function getAtendeeStatus($event_id, $crm_id, $user_country_code){

        // get configuration values
        $config = \Drupal::config('event_registration.settings');
        $client_env = $config->get('event_registration_environment_attendee_stat_select');
        $client_id = $config->get('event_registration_client_id');
        $client_secret = $config->get('event_registration_client_secret');
        if(!$client_id || !$client_secret) return [];
        
        //change URL depending on the link provided
        $takeda_id_endpoint_url = '';
        if($client_env == "dev") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_attendee_stat_dev');
        } else if ($client_env == "sit") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_attendee_stat_sit');
        } else if ($client_env == "uat") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_attendee_stat_uat');
        } else if ($client_env == "prod") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_attendee_stat_prod');
        } else {
            return [];
        }
        if(!$takeda_id_endpoint_url ) return [];
    
        $urlToSend = $takeda_id_endpoint_url . "?activityId=".$event_id."&takedaEnterpriseId=".$crm_id."&countryCode=".$user_country_code."&attendeeType=Account";
    
        $client = \Drupal::httpClient();
        try{
            $request = $client->request('GET', $urlToSend, [
                "headers" => [
                    "client_id" => $client_id,
                    "client_secret" => $client_secret,
                    "Content-Type" => "application/json",
                ]
            ]);
            
            $response = json_decode($request->getBody());

            return $response;
        }catch(\GuzzleHttp\Exception\RequestException $e){
            $response = $e->getResponse();

            if($response->getStatusCode() >= 300){
                return [];
            }
        }
        
    }

    public function getUserDigitalId($current_user_takeda_id){
        
        // get configuration values
        $config = \Drupal::config('event_registration.settings');
        $client_env = $config->get('event_registration_environment_takedaid_select');
        $client_id = $config->get('event_registration_client_id');
        $client_secret = $config->get('event_registration_client_secret');
        if(!$client_id || !$client_secret) return [];
        

        //change URL depending on the link provided
        $takeda_id_endpoint_url = '';
        if($client_env == "dev") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_takedaid_dev');
        } else if ($client_env == "sit") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_takedaid_sit');
        } else if ($client_env == "uat") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_takedaid_uat');
        } else if ($client_env == "prod") {
            $takeda_id_endpoint_url = $config->get('event_registration_environment_takedaid_prod');
        } else {
            return [];
        }
        if(!$takeda_id_endpoint_url ) return [];
    
        $urlToSend = $takeda_id_endpoint_url . "?search=id+eq+%22{$current_user_takeda_id}%22";
    
        $client = \Drupal::httpClient();
        $request = $client->request('GET', $urlToSend, [
            "headers" => [
                "client_id" => $client_id,
                "client_secret" => $client_secret,
                "Content-Type" => "application/json",
            ]
        ]);
        $response = json_decode($request->getBody())[0];
    
        return $response;
    }
    
}