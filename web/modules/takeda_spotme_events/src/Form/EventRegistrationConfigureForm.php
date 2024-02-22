<?php

namespace Drupal\spotme_events\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Plugin configuration form.
 */
class EventRegistrationConfigureForm extends ConfigFormBase
{
    /**
     * Determines the ID of a form.
     */
    public function getFormId()
    {
        return 'event_registration_configure';
    }

    /**
     * Gets the configuration names that will be editable.
     */
    public function getEditableConfigNames()
    {
        return [
            'event_registration.settings'
        ];
    }

    /**
     * Form constructor.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // $form['#attached'] = [
        //     'library' => [
        //         'event_registration/configuration'
        //     ]
        // ];

        // Read Settings.
        $config = $this->config('event_registration.settings');

        // General settings.
        $form['event_registration_client_credentials'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Client credentials')
        ]; 

        $form['event_registration_client_credentials']['event_registration_client_id'] = [
            '#id' => 'event_registration_client_id',
            '#type' => 'textfield',
            '#title' => $this->t('Client ID'),
            '#required' => TRUE,
            '#default_value' => (!empty($config->get('event_registration_client_id')) ? $config->get('event_registration_client_id') : ''),
        ]; 

        $form['event_registration_client_credentials']['event_registration_client_secret'] = [
            '#id' => 'event_registration_client_secret',
            '#type' => 'textfield',
            '#title' => $this->t('Client Secret'),
            '#required' => TRUE,
            '#default_value' => (!empty($config->get('event_registration_client_secret')) ? $config->get('event_registration_client_secret') : ''),
        ]; 

        $form['event_registration_takedaid_env'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('TakedaID environment settings')
        ]; 

        $form['event_registration_takedaid_env']['event_registration_environment_takedaid_select'] = [
            '#title'         => 'TakedaID environment',
            '#required'      =>  TRUE,
            '#type'          => 'select',
            '#empty_value'   => '',
            '#empty_option'  => 'None',
            '#size'          => 0,
            '#options'       => ['dev' => 'DEV', 'sit' => 'SIT', 'uat' => 'UAT', 'prod' => 'PROD',],
            '#default_value' => (!empty($config->get('event_registration_environment_takedaid_select')) ? $config->get('event_registration_environment_takedaid_select') : ''),
        ];

        $form['event_registration_takedaid_env']['event_registration_environment_takedaid_dev'] = [
            '#id' => 'event_registration_environment_takedaid_dev',
            '#type' => 'textfield',
            '#title' => $this->t('TakedaID DEV'),
            '#default_value' => (!empty($config->get('event_registration_environment_takedaid_dev')) ? $config->get('event_registration_environment_takedaid_dev') : ''),
        ]; 
        
        $form['event_registration_takedaid_env']['event_registration_environment_takedaid_sit'] = [
            '#id' => 'event_registration_environment_takedaid_sit',
            '#type' => 'textfield',
            '#title' => $this->t('TakedaID SIT'),
            '#default_value' => (!empty($config->get('event_registration_environment_takedaid_sit')) ? $config->get('event_registration_environment_takedaid_sit') : ''),
        ]; 
        
        $form['event_registration_takedaid_env']['event_registration_environment_takedaid_uat'] = [
            '#id' => 'event_registration_environment_takedaid_uat',
            '#type' => 'textfield',
            '#title' => $this->t('TakedaID UAT'),
            '#default_value' => (!empty($config->get('event_registration_environment_takedaid_uat')) ? $config->get('event_registration_environment_takedaid_uat') : ''),
        ];  
        
        $form['event_registration_takedaid_env']['event_registration_environment_takedaid_prod'] = [
            '#id' => 'event_registration_environment_takedaid_prod',
            '#type' => 'textfield',
            '#title' => $this->t('TakedaID PROD'),
            '#default_value' => (!empty($config->get('event_registration_environment_takedaid_prod')) ? $config->get('event_registration_environment_takedaid_prod') : ''),
        ]; 
        
        $form['event_registration_veeva_reg_env'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Veeva register query environment settings')
        ]; 

        $form['event_registration_veeva_reg_env']['event_registration_environment_veeva_select'] = [
            '#title'         => 'Veeva register environment',
            '#required'      =>  TRUE,
            '#type'          => 'select',
            '#empty_value'   => '',
            '#empty_option'  => 'None',
            '#size'          => 0,
            '#options'       => ['dev' => 'DEV', 'sit' => 'SIT', 'uat' => 'UAT', 'prod' => 'PROD',],
            '#default_value' => (!empty($config->get('event_registration_environment_veeva_select')) ? $config->get('event_registration_environment_veeva_select') : ''),
        ];

        $form['event_registration_veeva_reg_env']['event_registration_environment_veeva_dev'] = [
            '#id' => 'event_registration_environment_veeva_dev',
            '#type' => 'textfield',
            '#title' => $this->t('Veeva register DEV'),
            '#default_value' => (!empty($config->get('event_registration_environment_veeva_dev')) ? $config->get('event_registration_environment_veeva_dev') : ''),
        ]; 
        
        $form['event_registration_veeva_reg_env']['event_registration_environment_veeva_sit'] = [
            '#id' => 'event_registration_environment_veeva_sit',
            '#type' => 'textfield',
            '#title' => $this->t('Veeva register SIT'),
            '#default_value' => (!empty($config->get('event_registration_environment_veeva_sit')) ? $config->get('event_registration_environment_veeva_sit') : ''),
        ]; 
        
        $form['event_registration_veeva_reg_env']['event_registration_environment_veeva_uat'] = [
            '#id' => 'event_registration_environment_veeva_uat',
            '#type' => 'textfield',
            '#title' => $this->t('Veeva register UAT'),
            '#default_value' => (!empty($config->get('event_registration_environment_veeva_uat')) ? $config->get('event_registration_environment_veeva_uat') : ''),
        ];  
        
        $form['event_registration_veeva_reg_env']['event_registration_environment_veeva_prod'] = [
            '#id' => 'event_registration_environment_veeva_prod',
            '#type' => 'textfield',
            '#title' => $this->t('Veeva register PROD'),
            '#default_value' => (!empty($config->get('event_registration_environment_veeva_prod')) ? $config->get('event_registration_environment_veeva_prod') : ''),
        ]; 
        
        $form['event_registration_spotme_url_reg_env'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Spotme URL query environment settings')
        ]; 

        $form['event_registration_spotme_url_reg_env']['event_registration_environment_spotme_url_select'] = [
            '#title'         => 'Spotme URL environment',
            '#required'      =>  TRUE,
            '#type'          => 'select',
            '#empty_value'   => '',
            '#empty_option'  => 'None',
            '#size'          => 0,
            '#options'       => ['dev' => 'DEV', 'sit' => 'SIT', 'uat' => 'UAT', 'prod' => 'PROD',],
            '#default_value' => (!empty($config->get('event_registration_environment_spotme_url_select')) ? $config->get('event_registration_environment_spotme_url_select') : ''),
        ];

        $form['event_registration_spotme_url_reg_env']['event_registration_environment_spotme_url_dev'] = [
            '#id' => 'event_registration_environment_spotme_url_dev',
            '#type' => 'textfield',
            '#title' => $this->t('Spotme URL DEV'),
            '#default_value' => (!empty($config->get('event_registration_environment_spotme_url_dev')) ? $config->get('event_registration_environment_spotme_url_dev') : ''),
        ]; 
        
        $form['event_registration_spotme_url_reg_env']['event_registration_environment_spotme_url_sit'] = [
            '#id' => 'event_registration_environment_spotme_url_sit',
            '#type' => 'textfield',
            '#title' => $this->t('Spotme URL SIT'),
            '#default_value' => (!empty($config->get('event_registration_environment_spotme_url_sit')) ? $config->get('event_registration_environment_spotme_url_sit') : ''),
        ]; 
        
        $form['event_registration_spotme_url_reg_env']['event_registration_environment_spotme_url_uat'] = [
            '#id' => 'event_registration_environment_spotme_url_uat',
            '#type' => 'textfield',
            '#title' => $this->t('Spotme URL UAT'),
            '#default_value' => (!empty($config->get('event_registration_environment_spotme_url_uat')) ? $config->get('event_registration_environment_spotme_url_uat') : ''),
        ];  
        
        $form['event_registration_spotme_url_reg_env']['event_registration_environment_spotme_url_prod'] = [
            '#id' => 'event_registration_environment_spotme_url_prod',
            '#type' => 'textfield',
            '#title' => $this->t('Spotme URL PROD'),
            '#default_value' => (!empty($config->get('event_registration_environment_spotme_url_prod')) ? $config->get('event_registration_environment_spotme_url_prod') : ''),
        ]; 

        $form['event_registration_attendee_stat_env'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Attendee status query environment settings')
        ]; 

        $form['event_registration_attendee_stat_env']['event_registration_environment_attendee_stat_select'] = [
            '#title'         => 'Attendee status environment',
            '#required'      =>  TRUE,
            '#type'          => 'select',
            '#empty_value'   => '',
            '#empty_option'  => 'None',
            '#size'          => 0,
            '#options'       => ['dev' => 'DEV', 'sit' => 'SIT', 'uat' => 'UAT', 'prod' => 'PROD',],
            '#default_value' => (!empty($config->get('event_registration_environment_attendee_stat_select')) ? $config->get('event_registration_environment_attendee_stat_select') : ''),
        ];

        $form['event_registration_attendee_stat_env']['event_registration_environment_attendee_stat_dev'] = [
            '#id' => 'event_registration_environment_attendee_stat_dev',
            '#type' => 'textfield',
            '#title' => $this->t('Attendee status DEV'),
            '#default_value' => (!empty($config->get('event_registration_environment_attendee_stat_dev')) ? $config->get('event_registration_environment_attendee_stat_dev') : ''),
        ]; 
        
        $form['event_registration_attendee_stat_env']['event_registration_environment_attendee_stat_sit'] = [
            '#id' => 'event_registration_environment_attendee_stat_sit',
            '#type' => 'textfield',
            '#title' => $this->t('Attendee status SIT'),
            '#default_value' => (!empty($config->get('event_registration_environment_attendee_stat_sit')) ? $config->get('event_registration_environment_attendee_stat_sit') : ''),
        ]; 
        
        $form['event_registration_attendee_stat_env']['event_registration_environment_attendee_stat_uat'] = [
            '#id' => 'event_registration_environment_attendee_stat_uat',
            '#type' => 'textfield',
            '#title' => $this->t('Attendee status UAT'),
            '#default_value' => (!empty($config->get('event_registration_environment_attendee_stat_uat')) ? $config->get('event_registration_environment_attendee_stat_uat') : ''),
        ];  
        
        $form['event_registration_attendee_stat_env']['event_registration_environment_attendee_stat_prod'] = [
            '#id' => 'event_registration_environment_attendee_stat_prod',
            '#type' => 'textfield',
            '#title' => $this->t('Attendee status PROD'),
            '#default_value' => (!empty($config->get('event_registration_environment_attendee_stat_prod')) ? $config->get('event_registration_environment_attendee_stat_prod') : ''),
        ]; 
        
        $form['event_registration_field_settings'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Module settings')
        ];

        $form['event_registration_field_settings']['event_registration_login_url'] = [
            '#id' => 'event_registration_login_url',
            '#type' => 'textfield',
            '#title' => $this->t('Login URL'),
            '#default_value' => (!empty($config->get('event_registration_login_url')) ? $config->get('event_registration_login_url') : ''),
        ]; 
        
        $form['event_registration_field_settings']['event_registration_event_colour'] = [
            '#id' => 'event_registration_event_colour',
            '#type' => 'textfield',
            '#title' => $this->t('HEX colour'),
            '#default_value' => (!empty($config->get('event_registration_event_colour')) ? $config->get('event_registration_event_colour') : ''),
        ];

         $form['event_registration_field_settings']['event_registration_stakeholder_email'] = [
            '#id' => 'event_registration_stakeholder_email',
            '#type' => 'email',
            '#title' => $this->t('Stakeholder email'),
            '#default_value' => (!empty($config->get('event_registration_stakeholder_email')) ? $config->get('event_registration_stakeholder_email') : ''),
        ];

        $form['event_registration_field_settings']['manual_email'] = [
            '#type'  => 'submit',
            '#value' => $this->t('Send Manual Teams Email'),
            '#submit' => ['::manualEmail'],
        ];

        // Form submit button
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save Settings'),
            '#button_type' => 'primary'
        ];
       
        return $form;
    }

    /**
     * {@inheritdoc}
     */

    public function manualEmail(array &$form, FormStateInterface $form_state)
    {
        // Get the data from the module
        $config = \Drupal::config('event_registration.settings');

        // Set variables from module
        $stakeholder_email = $config->get('event_registration_stakeholder_email');

        $send_email = \Drupal::service('event_registration.email_functions')->triggerTeamsEmail($stakeholder_email);

        \Drupal::messenger()->addMessage($this->t($send_email),'status',TRUE);
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->config('event_registration.settings');

        //Module custom settings
        $config->set('event_registration_event_colour', $form_state->getValue('event_registration_event_colour'));
        $config->set('event_registration_login_url', $form_state->getValue('event_registration_login_url'));
        $config->set('event_registration_stakeholder_email', $form_state->getValue('event_registration_stakeholder_email'));

        //Client credentials
        $config->set('event_registration_client_id', $form_state->getValue('event_registration_client_id'));
        $config->set('event_registration_client_secret', $form_state->getValue('event_registration_client_secret'));
        
        //TakedaID env
        $config->set('event_registration_environment_takedaid_select', $form_state->getValue('event_registration_environment_takedaid_select'));
        $config->set('event_registration_environment_takedaid_dev', $form_state->getValue('event_registration_environment_takedaid_dev'));
        $config->set('event_registration_environment_takedaid_sit', $form_state->getValue('event_registration_environment_takedaid_sit'));
        $config->set('event_registration_environment_takedaid_uat', $form_state->getValue('event_registration_environment_takedaid_uat'));
        $config->set('event_registration_environment_takedaid_prod', $form_state->getValue('event_registration_environment_takedaid_prod'));
        
        //Veeva env
        $config->set('event_registration_environment_veeva_select', $form_state->getValue('event_registration_environment_veeva_select'));
        $config->set('event_registration_environment_veeva_dev', $form_state->getValue('event_registration_environment_veeva_dev'));
        $config->set('event_registration_environment_veeva_sit', $form_state->getValue('event_registration_environment_veeva_sit'));
        $config->set('event_registration_environment_veeva_uat', $form_state->getValue('event_registration_environment_veeva_uat'));
        $config->set('event_registration_environment_veeva_prod', $form_state->getValue('event_registration_environment_veeva_prod'));
        
        //Spotme env
        $config->set('event_registration_environment_spotme_url_select', $form_state->getValue('event_registration_environment_spotme_url_select'));
        $config->set('event_registration_environment_spotme_url_dev', $form_state->getValue('event_registration_environment_spotme_url_dev'));
        $config->set('event_registration_environment_spotme_url_sit', $form_state->getValue('event_registration_environment_spotme_url_sit'));
        $config->set('event_registration_environment_spotme_url_uat', $form_state->getValue('event_registration_environment_spotme_url_uat'));
        $config->set('event_registration_environment_spotme_url_prod', $form_state->getValue('event_registration_environment_spotme_url_prod'));

        //Attendee env
        $config->set('event_registration_environment_attendee_stat_select', $form_state->getValue('event_registration_environment_attendee_stat_select'));
        $config->set('event_registration_environment_attendee_stat_dev', $form_state->getValue('event_registration_environment_attendee_stat_dev'));
        $config->set('event_registration_environment_attendee_stat_sit', $form_state->getValue('event_registration_environment_attendee_stat_sit'));
        $config->set('event_registration_environment_attendee_stat_uat', $form_state->getValue('event_registration_environment_attendee_stat_uat'));
        $config->set('event_registration_environment_attendee_stat_prod', $form_state->getValue('event_registration_environment_attendee_stat_prod'));
        
        $config->save();
        return parent::submitForm($form, $form_state);
    }
}