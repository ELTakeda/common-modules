<?php
namespace Drupal\takeda_alchemer\Service;

use Drupal\node\NodeInterface;
use Drupal\takeda_alchemer\AlchemerFieldsConfig as Config;

class Helper extends Config
{   

    /**
     * getCode
     *
     * @param  mixed $param
     * @param  mixed $type
     * @return void
     */
    public function getCode($param, $type){
        $param = explode(' - ', $param);
        $code = '';
        $values = $this->getConfigValues($type);

        if (!empty($values)) {
          $items = explode("\n", $values);
          $converted_items = array_map(fn($val) => explode(";", trim($val)), $items);
        
          foreach ($converted_items as $entity) {  

            if (in_array($param[0], $entity)) {

              if (isset($entity[1])) {
                $code = $entity[1];
              }

            }

          }
        }
        return $code;
      }
      
      /**
       * Extracts information from the alchemer tables
       * based on the survey type.
       * 
       * @param mixed $node
       * @param string $type
       * @return array
       */
      
    public function retrieveSurveyData(NodeInterface $node, $type) {
        $result = [];
        $nid = $node->id();

        if(!empty($nid)) {
          $database = \Drupal::database();
          $query = $database->select('takeda_alchemer_'.$type.'', 'f')
          ->fields('f')
          ->condition('nid', $nid)
          ->execute()
          ->fetchAssoc();

          if ($query) {
            $result = $query;
          }
        }

        return $result;
    }
    
    /**
     * checkItemExist
     * Check input param exist in alchemer configuration.
     *
     * @param  string $param
     * @param  string  $type
     * @return bool
     */
    public function checkItemExist($param, $type) {
        //label - 0, code - 1
        $param = explode(' - ', $param);
        $itemExist = false;
        $values = $this->getConfigValues($type);

        if (!empty($values)) {

          $items = array_filter(explode("\n", $values));
          $converted_items = array_map(fn($val) => explode(";", trim($val)), $items);
        
          foreach ($converted_items as $entity) {    

            if (in_array(trim($param[0]), $entity)) {
              $itemExist = true;
              //check if user try to save wrong code 
              if (isset($param[1])){

                if (!in_array($param[1], $entity)) {
                  $itemExist = false;
                }
              }
              break;
            }
          }
        }
        return $itemExist;
    }
    
    /**
     * getConfigValues
     * Choose which configuration to get
     *
     * @param  string $type
     * @return string
     */
    public function getConfigValues($type) {
      $values = [];

      $country = self::FIELD_ALCHEMER_COUNTRY_NAME['name'];
      $therapy_area = self::FIELD_ALCHEMER_THERAPY_AREA['name'];
      $website_product = self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name'];
      $function = self::FIELD_ALCHEMER_FUNCTION['name'];
      $section = self::FIELD_MICROFEEDBACK_SECTION['name'];

      $config = \Drupal::config('takeda_alchemer.settings');
      $data = $config->get('takeda_alchemer_values');

      switch ($type) {
        case $therapy_area:
          $values = $data['takeda_alchemer_therapy_areas'];
          break;
    
        case $website_product:
          $values = $data['takeda_alchemer_products'];
          break;
    
        case $country:
          $values = $data['takeda_alchemer_country_codes'];
          break;
        
        case $function:
          $values = $data['takeda_alchemer_function_labels'];
          break;
        
        case $section:
          $values = $data['takeda_alchemer_section_labels'];
          break;
      }
      return $values;
    }

    /**
     * Retrieve takeda id by given user id
     * @param mixed $user_id
     * 
     * @return string|null
     */
    public function retrieveTakedaId($user_id) {
      $database = \Drupal::database();
      $schema = $database->schema();
      $takeda_id = null;

      //check if takedaId table exists because we extract table data from another custom module
      if ($schema->tableExists('user__field_takeda_id')) {
          $result = $database->select('user__field_takeda_id', 'f')
          ->fields('f', ['field_takeda_id_value'])
          ->condition('entity_id', $user_id)
          ->execute()
          ->fetchField();

          if ($result) {
            $takeda_id = $result;
          }
      }
      
      return $takeda_id;
    }

    /**
     * Retrive digital id
     * @param string $current_user_takeda_id
     * 
     * @return array
     */
    public function getUserDigitalId($current_user_takeda_id) {
      $parameters_for_beacons = [];

      $moduleHandler = \Drupal::service('module_handler');
      //prevent unexpected erorrs due missing module
      if ($moduleHandler->moduleExists('takeda_id')) {
        
        //get takeda_id conf keys
        $takeda_id_config = \Drupal::config('takeda_id.settings');
        $client_id = $takeda_id_config->get('api_key');
        $client_secret = $takeda_id_config->get('api_secret');

        // get alchemer config value for client environment
        $config = \Drupal::config('takeda_alchemer.settings');
        $data = $config->get('takeda_alchemer_values');
        $client_env = $data['takeda_alchemer_environment'];

        if (!$client_id || !$client_secret) return [];

        $takeda_id_endpoint_url = '';
        if ($client_env == "dev") {
            $takeda_id_endpoint_url = Config::TAKEDA_ID_ENVIRONMENT_URL_DEV;
        } else if ($client_env == "sit") {
            $takeda_id_endpoint_url = Config::TAKEDA_ID_ENVIRONMENT_URL_SIT;
        } else if ($client_env == "uat") {
            $takeda_id_endpoint_url = Config::TAKEDA_ID_ENVIRONMENT_URL_UAT;
        } else if ($client_env == "prod") {
            $takeda_id_endpoint_url = Config::TAKEDA_ID_ENVIRONMENT_URL_PROD;
        } else {
            return [];
        }

        $urlToSend = $takeda_id_endpoint_url . "?search=id+eq+%22{$current_user_takeda_id}%22";

        $client = \Drupal::httpClient();
        $request = $client->request('GET', $urlToSend, [
            "headers" => [
                "client_id" => $client_id,
                "client_secret" => $client_secret,
                "Content-Type" => "application/json",
            ]
        ]);

        if (!empty(json_decode($request->getBody(), true))) {
          $response = json_decode($request->getBody())[0];

          $customer_id = self::FIELD_ALCHEMER_CUSTOMER_ID['name'];
          $digital_id = self::FIELD_ALCHEMER_DIGITAL_ID['name'];

          if (isset($response->profile->digital_id)) {
              $parameters_for_beacons[$customer_id] = $response->profile->digital_id;
          }
          if (isset($response->profile->customer_id)) {
              $parameters_for_beacons[$digital_id] = $response->profile->customer_id;
          }
        }

      }
      
      return $parameters_for_beacons;
    }
}