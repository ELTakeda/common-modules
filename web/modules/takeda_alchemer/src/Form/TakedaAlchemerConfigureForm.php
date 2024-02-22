<?php

namespace Drupal\takeda_alchemer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\takeda_alchemer\AlchemerFieldsConfig as Config;


/**
 * class TakedaAlchemerConfigureForm
 */
class TakedaAlchemerConfigureForm extends ConfigFormBase
{

    /**
     * Determines the ID of a form.
     * @return string
     */
    public function getFormId()
    {
        return 'takeda_alchemer_configure';
    }
    

    /**
     * Gets the configuration names that will be editable.
     * @return array
     */
    public function getEditableConfigNames()
    {
        return [
            'takeda_alchemer.settings'
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
        $config = $this->config('takeda_alchemer.settings');
        $takeda_alchemer_values = $config->get('takeda_alchemer_values') ?: []; // Blank array if no data so that it can be countable

        $form['takeda_alchemer_basic_settings'] = [
            '#type' => 'fieldset',
            '#tree' => true
        ];

        $form['takeda_alchemer_basic_settings']['takeda_alchemer_environment'] = [
            '#id' => 'takeda_alchemer_environment',
            '#type' => 'select',
            '#title' => $this->t('Select environment'),
            '#required' => TRUE,
            '#description' => $this->t('By this selection you choose from which environment in 
                            Takeda ID API you will be getting digital_id and customer_id for the logged in user.'),
            '#options' => [
                'dev' => $this->t('Dev'),
                'sit' => $this->t('Sit'),
                'uat' => $this->t('UAT'),
                'prod' => $this->t('Production'),
            ],
            '#default_value' => (!empty($takeda_alchemer_values['takeda_alchemer_environment']) ? $takeda_alchemer_values['takeda_alchemer_environment'] : ''),

        ];

        $form['takeda_alchemer_basic_settings']['takeda_alchemer_microfeedback_survey_url'] = [
            '#id' => 'takeda_alchemer_microfeedback_survey_url',
            '#type' => 'textfield',
            '#title' => $this->t('Microfeedback survey basic url'),
            '#default_value' => (!empty($takeda_alchemer_values['takeda_alchemer_microfeedback_survey_url']) ? $takeda_alchemer_values['takeda_alchemer_microfeedback_survey_url'] : ''),
            '#size' => 60,
            '#description' => $this->t('Url used for microfeedback iframe'),
        ];

        $form['takeda_alchemer_basic_settings']['takeda_alchemer_site_id'] = [
            '#id' => 'takeda_alchemer_site_id',
            '#type' => 'textfield',
            '#title' => $this->t('Site ID'),
            '#required' => TRUE,
            '#default_value' => (!empty($takeda_alchemer_values['takeda_alchemer_site_id']) ? $takeda_alchemer_values['takeda_alchemer_site_id'] : ''),
            '#size' => 60
        ];

        $form['takeda_alchemer_basic_settings']['takeda_alchemer_country'] = [
            '#id' => 'takeda_alchemer_country',
            '#type' => 'textfield',
            '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
            '#autocomplete_route_parameters' => array('field_name' => Config::FIELD_ALCHEMER_COUNTRY_NAME['name']),
            '#element_validate' => ['::validateCountry'],
            '#title' => $this->t('Country'),
            '#required' => TRUE,
            '#default_value' => (!empty($takeda_alchemer_values['takeda_alchemer_country']) ? $takeda_alchemer_values['takeda_alchemer_country'] : ''),
            '#size' => 60
        ];

        $form['takeda_alchemer_basic_settings']['takeda_alchemer_therapy_areas'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Therapy areas labels and codes'),
            '#description' => $this->t('Separate labels and codes with a semicolon. Every item(label;code) must be on new line.'),
            '#default_value' => (!empty($takeda_alchemer_values['takeda_alchemer_therapy_areas']) ? $takeda_alchemer_values['takeda_alchemer_therapy_areas'] : ''),
        ];

        $form['takeda_alchemer_basic_settings']['takeda_alchemer_products'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Products labels and codes'),
            '#description' => $this->t('Separate labels and codes with a semicolon. Every item(label;code) must be on new line.'),
            '#default_value' => (!empty($takeda_alchemer_values['takeda_alchemer_products']) ? $takeda_alchemer_values['takeda_alchemer_products'] : ''),
        ];

        $form['takeda_alchemer_basic_settings']['takeda_alchemer_country_codes'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Countries labels and codes'),
            '#required' => TRUE,
            '#description' => $this->t('Separate labels and codes with a semicolon. Every item(label;code) must be on new line.'),
            '#default_value' => (!empty($takeda_alchemer_values['takeda_alchemer_country_codes']) ? $takeda_alchemer_values['takeda_alchemer_country_codes'] : ''),  
        ];

        $form['takeda_alchemer_basic_settings']['takeda_alchemer_function_labels'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Function labels'),
            '#description' => $this->t('Every item must be on new line.'),
            '#default_value' => (!empty($takeda_alchemer_values['takeda_alchemer_function_labels']) ? $takeda_alchemer_values['takeda_alchemer_function_labels'] : ''),  
        ];

        $form['takeda_alchemer_basic_settings']['takeda_alchemer_section_labels'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Section labels'),
            '#description' => $this->t('Every item must be on new line.'),
            '#default_value' => (!empty($takeda_alchemer_values['takeda_alchemer_section_labels']) ? $takeda_alchemer_values['takeda_alchemer_section_labels'] : ''),  
        ];

        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save Settings') 
        ];

        return $form;
    }
    
    /**
     * validateCountry
     *
     * @param  array $form
     * @param  mixed $form_state
     * @return void
     */
    public function validateCountry(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValue(['takeda_alchemer_basic_settings']);
        $country = strtolower($values['takeda_alchemer_country']);
        if(!empty($country)) {
            $all_countries = $values['takeda_alchemer_country_codes'];
            $all_countries_str = strtolower(str_replace(["\n"], ' ', $all_countries));
            if (strpos($all_countries_str, $country) === FALSE) {
                $form_state->setError($form, t('This country is not in the list.'));
            }
        }
    }

    /**
     * Submit form logic
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->config('takeda_alchemer.settings');
        $values = $form_state->getValue(['takeda_alchemer_basic_settings']);
        $config->set('takeda_alchemer_values', $values);
        $config->save();
        return parent::submitForm($form, $form_state);
    }
}