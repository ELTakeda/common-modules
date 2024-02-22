<?php

namespace Drupal\sfmc\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;


class SfmcConfigurationForm extends ConfigFormBase
{

    /**
     * Determines the ID of a form.
     * @return string
     */
    public function getFormId()
    {
        return 'sfmc_configure';
    }
    

    /**
     * Gets the configuration names that will be editable.
     * @return array
     */
    public function getEditableConfigNames()
    {
        return [
            'sfmc.settings'
        ];
    }

    /**
     * Form constructor
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

        // Read Settings.
        $config = $this->config('sfmc.settings');

        // Getting the objects
        $client = \Drupal::httpClient();


        $website_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
// Forming the data for the object list
        $data_objectList = [
            "dynamicObjectHandler" => [
                "method" => "retrieve",
                "target" => "HIVE-US",
                "source" => $website_url
            ],
        ];
        //Extracting all the objects
        $objectList = $this->getObjectList($client, $config->get('object_api_url'), json_encode($data_objectList));
          $form['sfhc'] = array(
                '#id' => 'sfhc',
                '#type' => 'fieldset',
                '#title' => $this->t('SFHC settings'),
                '#collapsible' => true, // Added
                '#collapsed' => false, // Added
          );
          $form['sfhc']['object'] = array(
            '#id' => 'object',
            '#type' => 'select',
            '#title' => $this->t('Salesforce object'),
            '#options' => $objectList->sobjects,
            '#default_value' => (!empty($config->get('object_object')) ? $config->get('object_object') : ''),
        );
        $form['sfhc']['object_api_url'] = array(
            '#id' => 'object_api_url',
            '#type' => 'textfield',
            '#title' => $this->t('Object list API URL'),
            '#default_value' => (!empty($config->get('object_api_url')) ? $config->get('object_api_url') : ''),
        );
        $form['sfhc']['field_api_url'] = array(
            '#id' => 'field_api_url',
            '#type' => 'textfield',
            '#title' => $this->t('Field list API URL'),
            '#default_value' => (!empty($config->get('field_api_url')) ? $config->get('field_api_url') : ''),
        );
         $form['sfhc']['insert_api_url'] = array(
            '#id' => 'insert_api_url',
            '#type' => 'textfield',
            '#title' => $this->t('Create record API URL'),
            '#default_value' => (!empty($config->get('insert_api_url')) ? $config->get('insert_api_url') : ''),
        );
        $form['sfhc']['client_id_sfhc'] = array(
            '#id' => 'client_id_sfhc',
            '#type' => 'textfield',
            '#title' => $this->t('SFHC client id'),
            '#default_value' => (!empty($config->get('client_id_sfhc')) ? $config->get('client_id_sfhc') : ''),
        );
        $form['sfhc']['client_secret_sfhc'] = array(
            '#id' => 'client_secret_sfhc',
            '#type' => 'textfield',
            '#title' => $this->t('SFHC client secret'),
            '#default_value' => (!empty($config->get('client_secret_sfhc')) ? $config->get('client_secret_sfhc') : ''),
        );
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save Settings') 
        ];

        return $form;
    }
    
    /**
     * Submit form logic
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->config('sfmc.settings');
        $key_object = $form_state->getValue('object');
        $val_object = $form['sfhc']['object']['#options'][$key_object];
        $config->set('object', $val_object);
        $config->set('object_object', $form_state->getValue('object'));
        $config->set('object_api_url', $form_state->getValue('object_api_url'));
        $config->set('insert_api_url', $form_state->getValue('insert_api_url'));
        $config->set('field_api_url', $form_state->getValue('field_api_url'));
        $config->set('client_id_sfhc', $form_state->getValue('client_id_sfhc'));
        $config->set('client_secret_sfhc', $form_state->getValue('client_secret_sfhc'));
        $config->save();
       
        return parent::submitForm($form, $form_state);
    }
    
// function for getting all the objects
    public function getObjectList($client, $urlToSend, $data){
        $config = $this->config('sfmc.settings');
        $request = $client->post(empty($urlToSend) ? 'https://api-ch2-eu-np.takeda.com/uat/gcc-form-submission-api/v1/objects':  $urlToSend, [
            "headers" => [
                "client_id" => empty($config->get('client_id_sfhc')) ? 'b91da09c9637429c8b60ef093df56829': $config->get('client_id_sfhc'),
                "client_secret" => empty($config->get('client_secret_sfhc')) ? 'FA7bBe9D609A48c9907e837160309594': $config->get('client_secret_sfhc'),
                "x-correlation-id" => "12312312312",
                "Content-Type" => "application/json",
                "Accept" => "*/*",
        ],
            'body' => $data
    ]
        );
        
        $result = json_decode($request->getBody());
        return $result;
    }
}

