<?php

namespace Drupal\sfmc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SfmcFormValidation extends ControllerBase
{

    public function FormValidation()
    {
        $client = \Drupal::httpClient();
// extracting TakedaId if user is authenticated
        $logged_in = \Drupal::currentUser()->isAuthenticated();
        $okta_id = "";
        if ($logged_in) {
            $user_id = \Drupal::currentUser()->id();
            $database = \Drupal::database();
            $okta_id = $database->select('user__field_takeda_id', 'f')
                ->fields('f', ['field_takeda_id_value'])
                ->condition('entity_id', $user_id)
                ->execute()
                ->fetchField();
        }
       
        // get website name
        $website_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";


        $urlToSend = "https://api-us-np.takeda.com/dev/security-takedaid-api/v1/users?search=id+eq+%22{$okta_id}%22";

        

        // get customer id
        $customer_id = !empty($this->getTakedaID($client, $urlToSend)->profile->customer_id) ? $this->getTakedaID($client, $urlToSend)->profile->customer_id : "";

      


        // prepare the data for the field list request
        $data_objectListField = [
            "dynamicObjectHandler" => [
                "method" => "retrieve",
                "target" => "HIVE-US",
                "object" => \Drupal::config('sfmc.settings')->get('object'),
                "source" => $website_url,
            ],
        ];
 // generate uuid
 $uuid_service = \Drupal::service('uuid');
 $uuid = $uuid_service->generate();
        // get all the fields
        $objectFieldList = $this->getObjectFieldList($client, \Drupal::config('sfmc.settings')->get('field_api_url'), json_encode($data_objectListField), $uuid);
        
     
        $objectFields = $objectFieldList->fields;
        
        // getting the start date
        $start_date = (new \DateTime())->format('Y-m-d');
    

    //adding additional data which is not presented in the form
    
        $_POST['RecordTypeId'] = '0126g000000bUg6AAE';
        $_POST['TK_Consent_Start_date__c'] = $start_date;
        $_POST['TK_Patient_Source__c'] = $website_url;
        $_POST['TK_Patient_Consent__c'] = filter_var($_POST['TK_Patient_Consent__c'], FILTER_VALIDATE_BOOLEAN);
        

        // store all the fields in an array and then compare the keys from that array with the post from the form to fill dynamically the fields with the correct data
        $tmp_fields = [];
        foreach ($objectFields as $key => $value) {
            # code...
            $tmp_fields[] = $value->name;
        }
        $validated_data = [];

        foreach ($tmp_fields as $field) {

            if (isset($_POST[$field])) {

                $validated_data[$field] = $_POST[$field];

            }

        }
        $record_data = [];
        foreach ($validated_data as $key => $value) {
            # code...
            if (\DateTime::createFromFormat('Y-m-d', $value) !== false) {
                // it's a date
                $record_data[] = array(
                    'fieldName' => $key,
                    "fieldType"=> "Date",
                    'fieldValue' => $value,  
                );
              } elseif(is_bool($value)){
                $record_data[] = array(
                    'fieldName' => $key,
                    "fieldType"=> "Boolean",
                    'fieldValue' =>json_encode($value),  
                );
              } else{
            $record_data[] = array(
                'fieldName' => $key,
                'fieldValue' => $value,
            );
        }
        }
       
        $createRecord = [
            "dynamicObjectHandler" => [
                "method" => "create",
                "target" => "HIVE-US",
                "object" => \Drupal::config('sfmc.settings')->get('object'),
                "source" => $website_url,
                "createRecord" => $record_data,
            ],
        ];
        
        
        
        // creating the record
        $record = $this->CreateRecord($client, \Drupal::config('sfmc.settings')->get('insert_api_url'), json_encode($createRecord), $customer_id, $uuid);
    
    
        
        // getting the redirect url
        $url_redirect = $_POST['url'];

        return new RedirectResponse($url_redirect);
    }

    

    // function for getting TakedaId
    public function getTakedaID($client, $urlToSend)
    {
        $request = $client->request('GET', $urlToSend, [
            "headers" => [
                "client_id" => "6de33039647a44978484daa656d75f85",
                "client_secret" => "86797DCE82eA4976bf6a2690185db75F",
                "Content-Type" => "application/json",
            ],
        ]);
        $result = json_decode($request->getBody())[0];
        return $result;
    }
// function for getting the object fields
    public function getObjectFieldList($client, $urlToSend, $data, $uuid)
    {
        $request = $client->post($urlToSend, [
            "headers" => [
                "client_id" => \Drupal::config('sfmc.settings')->get('client_id_sfhc'),
                "client_secret" => \Drupal::config('sfmc.settings')->get('client_secret_sfhc'),
                "x-correlation-id" => $uuid,
                "Content-Type" => "application/json",
                "Accept" => "*/*",
            ],
            'body' => $data,
        ]
        );
        $result = json_decode($request->getBody());
        return $result;
    }
    //function for creating record
    public function CreateRecord($client, $urlToSend, $data, $customer_id, $uuid)
    {
        $request = $client->put($urlToSend, [
            "headers" => [
                "client_id" => \Drupal::config('sfmc.settings')->get('client_id_sfhc'),
                "client_secret" => \Drupal::config('sfmc.settings')->get('client_secret_sfhc'),
                "x-correlation-id" => $uuid,
                "Content-Type" => "application/json",
                "Accept" => "*/*",
            ],
            'body' => $data,
        ]
        );
        \Drupal::logger('sfmc')->notice('Takeda Customer ID ' . $customer_id . ', correlation id ' . $uuid . ' attempting to send the following data:<pre><code>' . print_r(json_decode($data), true) . '</code></pre>');
        $response = $request;
        $response_status_code = $response->getStatusCode();
        if ($response_status_code == 200 || $response_status_code == 201) {
            \Drupal::logger('sfmc')->notice('Takeda Customer ID ' . $customer_id . ', correlation id ' . $uuid . ' has submitted the form');
            \Drupal::messenger()->addStatus($this->t('Your data was received successfully.'));
        } else {
            \Drupal::logger('sfmc')->error('Takeda Customer ID ' . $customer_id . ', correlation id ' . $uuid . ' has submitted the form but received an error.');
            \Drupal::messenger()->addStatus($this->t('An error occurred when receiving your data.'));
        }
        $result = json_decode($request->getBody());

        return $result;
    }

}