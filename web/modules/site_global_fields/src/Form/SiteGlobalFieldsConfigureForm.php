<?php

namespace Drupal\site_global_fields\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin configuration form.
 */
class SiteGlobalFieldsConfigureForm extends ConfigFormBase
{
    /**
     * Determines the ID of a form.
     */
    public function getFormId()
    {
        return 'site_global_fields_configure';
    }

    /**
     * Gets the configuration names that will be editable.
     */
    public function getEditableConfigNames()
    {
        return [
            'site_global_fields.settings'
        ];
    }

    /**
     * Form constructor.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Get the saved settings.
        $config = $this->config('site_global_fields.settings');
        $site_global_fields_values = $config->get('site_global_fields_values') ?: []; // Blank array if no data so that it can be countable

        // Creathe the fieldset that contains all fields.
        $form['site_global_fields_settings'] = [
            '#type' => 'fieldset',
            '#title' => 'Site Global Fields',
            '#tree' => true
        ];  

        //Fieldset for Switch popup
        $form['site_global_fields_settings']['site_reset_pass_fields'] = [
            '#type' => 'fieldset',
            '#title' => 'Reset passowrd fields'
        ];
        
        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_header'] = [
            '#type' => 'textfield',
            '#title' => 'Header',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_header']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_header'] : ''
        ]; 
        
        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_title'] = [
            '#type' => 'textfield',
            '#title' => 'Title',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_title']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_title'] : ''
        ]; 

        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_error_validation'] = [
            '#type' => 'textfield',
            '#title' => 'Error - validation',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_validation']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_validation'] : ''
        ]; 
        
        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_pass_label'] = [
            '#type' => 'textfield',
            '#title' => 'New password placeholder',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_pass_label']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_pass_label'] : ''
        ];  

        
        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_error_length'] = [
            '#type' => 'textfield',
            '#title' => 'Error - password length',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_length']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_length'] : ''
        ]; 
        
        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_error_uppercase'] = [
            '#type' => 'textfield',
            '#title' => 'Error - password uppercase',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_uppercase']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_uppercase'] : ''
        ]; 
        
        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_error_lowercase'] = [
            '#type' => 'textfield',
            '#title' => 'Error - password lowercase',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_lowercase']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_lowercase'] : ''
        ];  
        
        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_error_number'] = [
            '#type' => 'textfield',
            '#title' => 'Error - password number',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_number']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_number'] : ''
        ]; 

        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_error_special'] = [
            '#type' => 'textfield',
            '#title' => 'Error - password special',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_special']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_special'] : ''
        ]; 

        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_error_empty'] = [
            '#type' => 'textfield',
            '#title' => 'Error - password empty',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_empty']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_empty'] : ''
        ]; 
        
        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_pass2_label'] = [
            '#type' => 'textfield',
            '#title' => 'Confirm password placeholder',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_pass2_label']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_pass2_label'] : ''
        ]; 

        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_error_pass2_empty'] = [
            '#type' => 'textfield',
            '#title' => 'Error - confirm password empty',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_pass2_empty']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_error_pass2_empty'] : ''
        ]; 

        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_btn_label'] = [
            '#type' => 'textfield',
            '#title' => 'Submit button label',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_btn_label']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_btn_label'] : ''
        ];        

        $form['site_global_fields_settings']['site_reset_pass_fields']['site_reset_pass_text'] = [
            '#type' => 'text_format',
            '#format' => 'basic_html',
            '#basetype' => 'textarea',
            '#title' => 'Text under submit button',
            '#default_value' => !empty($site_global_fields_values['site_reset_pass_fields']['site_reset_pass_text']['value']) ? $site_global_fields_values['site_reset_pass_fields']['site_reset_pass_text']['value'] : ''
        ];

        $form['site_global_fields_settings']['site_captcha_fields'] = [
            '#type' => 'fieldset',
            '#title' => 'Captcha field'
        ];

        $form['site_global_fields_settings']['site_captcha_fields']['site_captcha_key'] = [
            '#type' => 'textfield',
            '#title' => 'Google captha key',
            '#default_value' => !empty($site_global_fields_values['site_captcha_fields']['site_captcha_key']) ? $site_global_fields_values['site_captcha_fields']['site_captcha_key'] : ''
        ];    
        
        $form['site_global_fields_settings']['site_eternal_urls'] = [
            '#type' => 'fieldset',
            '#title' => 'Eternal URL'
        ];
        
        $form['site_global_fields_settings']['site_eternal_urls']['site_eternal_url1'] = [
            '#type' => 'textfield',
            '#title' => 'URL1',
            '#default_value' => !empty($site_global_fields_values['site_eternal_urls']['site_eternal_url1']) ? $site_global_fields_values['site_eternal_urls']['site_eternal_url1'] : ''
        ]; 
        
        $form['site_global_fields_settings']['site_eternal_urls']['site_eternal_url2'] = [
            '#type' => 'textfield',
            '#title' => 'URL2',
            '#default_value' => !empty($site_global_fields_values['site_eternal_urls']['site_eternal_url2']) ? $site_global_fields_values['site_eternal_urls']['site_eternal_url2'] : ''
        ]; 
        
        $form['site_global_fields_settings']['site_eternal_urls']['site_eternal_url3'] = [
            '#type' => 'textfield',
            '#title' => 'URL3',
            '#default_value' => !empty($site_global_fields_values['site_eternal_urls']['site_eternal_url3']) ? $site_global_fields_values['site_eternal_urls']['site_eternal_url3'] : ''
        ]; 
        
        $form['site_global_fields_settings']['site_eternal_urls']['site_eternal_url4'] = [
            '#type' => 'textfield',
            '#title' => 'URL4',
            '#default_value' => !empty($site_global_fields_values['site_eternal_urls']['site_eternal_url4']) ? $site_global_fields_values['site_eternal_urls']['site_eternal_url4'] : ''
        ]; 
        
        $form['site_global_fields_settings']['site_eternal_urls']['site_eternal_url5'] = [
            '#type' => 'textfield',
            '#title' => 'URL5',
            '#default_value' => !empty($site_global_fields_values['site_eternal_urls']['site_eternal_url5']) ? $site_global_fields_values['site_eternal_urls']['site_eternal_url5'] : ''
        ]; 
        
        $form['site_global_fields_settings']['site_modal_title'] = [
            '#type' => 'textfield',
            '#title' => 'Title',
            '#default_value' => !empty($site_global_fields_values['site_modal_title']) ? $site_global_fields_values['site_modal_title'] : ''
        ]; 

        $form['site_global_fields_settings']['site_modal_text'] = [
            '#type' => 'text_format',
            '#title' => 'Text',
            '#format' => 'full_html',
            '#default_value' => !empty($site_global_fields_values['site_modal_text']['value']) ? $site_global_fields_values['site_modal_text']['value'] : '',
        ];
       

        // Form submit button
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => 'Save Settings',
            '#button_type' => 'primary'
        ];
       
        return $form;
    }

    /**
     * {@inheritdoc}
     */

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $values = $form_state->getValue(['site_global_fields_settings']);
        $config = \Drupal::service('config.factory')->getEditable('site_global_fields.settings');
        $config->set('site_global_fields_values', $values);
        $config->save();
        return parent::submitForm($form, $form_state);
    }
}