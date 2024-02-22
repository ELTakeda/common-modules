<?php

namespace Drupal\takeda_consents\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * TakedaConsentsPurposesConfigureForm
 * Plugin configuration form.
 */
class TakedaConsentsPurposesConfigureForm extends ConfigFormBase
{
    // Default method required for the form module to work
    public function getFormId()
    {
        return 'takeda_consents_configure';
    }

    // Default method required for the form module to work
    public function getEditableConfigNames()
    {
        return [
            'takeda_consents.settings'
        ];
    }
    
    // Build form method, renders and rerenders the form
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Initial data
        $form['description'] = [
            '#markup' => '<div>' . 'Add the consents to be supported by the site and their respective URLs and credentials.' . '</div>',
        ];
        $form['#tree'] = TRUE;

        // Get the saved data of the config
        $config = $this->config('takeda_consents.settings');
        $takeda_consents_values = $config->get('takeda_consents_values') ?: []; // Takeda consents values check - blank array if no data so that it can be countable
        $temporary_fields = $form_state->get('takeda_consents_temporary_fields') ?: [];
        $temporary_fields_to_delete = $form_state->get('takeda_consents_temporary_fields_to_remove');

        // The general fieldset wrapper
        $form['takeda_consents_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => 'Shared configuration and individual purposes',
            '#prefix' => '<div id="takeda-consents-fieldset-wrapper">',
            '#suffix' => '</div>',
        ];

        $form['takeda_consents_fieldset']['omnichannel_url'] = [
            '#type' => 'textfield',
            '#title' => 'Omnichannel URL',
            '#description' => 'The URL for the environment that the data has to be sent to',
            '#default_value' => (!empty($takeda_consents_values['omnichannel_url'])) ? $takeda_consents_values['omnichannel_url'] : '',
            '#required' => TRUE,
        ];

        $form['takeda_consents_fieldset']['omnichannel_api_key'] = [
            '#type' => 'textfield',
            '#title' => 'Omnichannel API Key',
            '#description' => 'The API key for the environment that the data has to be sent to',
            '#default_value' => (!empty($takeda_consents_values['omnichannel_api_key'])) ? $takeda_consents_values['omnichannel_api_key'] : '',
            '#required' => TRUE,
        ];

        $form['takeda_consents_fieldset']['omnichannel_api_secret'] = [
            '#type' => 'textfield',
            '#title' => 'Omnichannel API Secret',
            '#description' => 'The API secret for the environment that the data has to be sent to',
            '#default_value' => (!empty($takeda_consents_values['omnichannel_api_secret'])) ? $takeda_consents_values['omnichannel_api_secret'] : '',
            '#required' => TRUE,
        ];

        // Loop used to create all purpose fieldsets (purpose field groups)
        $takeda_consents_purposes = isset($takeda_consents_values['takeda_consents_purposes']) ? $takeda_consents_values['takeda_consents_purposes'] : [];

        if (!empty($temporary_fields)) {
            $takeda_consents_purposes = array_merge($takeda_consents_purposes, $temporary_fields);
        }
        
        $i = 0;
        for ($i = 0; $i < count($takeda_consents_purposes); $i++) {
            if (!empty($temporary_fields_to_delete) && in_array($i, $temporary_fields_to_delete)) {
                continue;
            }
            
            $current_takeda_consents_value = $takeda_consents_purposes[$i];

            $form['takeda_consents_fieldset']['takeda_consents_purposes'][$i] = [
                '#type' => 'fieldset',
                '#title' => 'Each purpose configuration data',
                '#prefix' => '<div id="takeda-consents-individual-fieldset-wrapper-' . $i . '">',
                '#suffix' => '</div>',
            ];

            // Generate all the fields for the fieldset
            $form['takeda_consents_fieldset']['takeda_consents_purposes'][$i]['request_info'] = [
                '#type' => 'textfield',
                '#title' => 'Purpose Request Info / OneTrust API Token',
                '#description' => 'This field is received from the OneTrust support and is unique for each purpose',
                '#default_value' => (!empty($current_takeda_consents_value['request_info'])) ? $current_takeda_consents_value['request_info'] : '',
                '#required' => TRUE,
                '#maxlength' => 4000
            ];

            $form['takeda_consents_fieldset']['takeda_consents_purposes'][$i]['request_info_second'] = [
                '#type' => 'textfield',
                '#title' => 'Purpose Request Info / OneTrust API Token - Double Opt In',
                '#description' => 'This field is received from the OneTrust support and is unique for each purpose',
                '#default_value' => (!empty($current_takeda_consents_value['request_info_second'])) ? $current_takeda_consents_value['request_info_second'] : '',
                '#maxlength' => 4000
            ];

            $form['takeda_consents_fieldset']['takeda_consents_purposes'][$i]['request_info_mobile'] = [
                '#type' => 'textfield',
                '#title' => 'Purpose Request Info / OneTrust API Token - Mobile',
                '#description' => 'This field is received from the OneTrust support and is unique for each purpose',
                '#default_value' => (!empty($current_takeda_consents_value['request_info_mobile'])) ? $current_takeda_consents_value['request_info_mobile'] : '',
                '#maxlength' => 4000
            ];
            
            $form['takeda_consents_fieldset']['takeda_consents_purposes'][$i]['purpose_id'] = [
                '#type' => 'textfield',
                '#title' => 'Purpose ID',
                '#description' => 'The Purpose ID of the consent that you want to add as defined in OneTrust',
                '#default_value' => (!empty($current_takeda_consents_value['purpose_id'])) ? $current_takeda_consents_value['purpose_id'] : '',
                '#required' => TRUE,
            ];

            $form['takeda_consents_fieldset']['takeda_consents_purposes'][$i]['purpose_name'] = [
                '#type' => 'textfield',
                '#title' => 'Purpose Name',
                '#description' => 'The Purpose Name of the consent that you want to be displayed to the user and some site configurations',
                '#default_value' => (!empty($current_takeda_consents_value['purpose_name'])) ? $current_takeda_consents_value['purpose_name'] : '',
                '#required' => TRUE,
            ];

            // Purpose identifier select - email, takedaid
            $form['takeda_consents_fieldset']['takeda_consents_purposes'][$i]['purpose_identifier'] = [
                '#type' => 'select',
                '#title' => 'Purpose Identifier',
                '#description' => 'The Purpose Identifier may be different for each purpose and is used in the process of sending a request',
                '#empty_option' => '',
                '#default_value' => (!empty($current_takeda_consents_value['purpose_identifier'])) ? $current_takeda_consents_value['purpose_identifier'] : '',
                '#options' => [
                    'email' => 'E-mail',
                    'takedaid' => 'Takeda ID',
                ],
                '#required' => TRUE,
            ];

            $form['takeda_consents_fieldset']['takeda_consents_purposes'][$i]['purpose_preference_boolean'] = [
                '#type' => 'checkbox',
                '#title' => 'The Purpose has Preferences and/or Options',
                '#default_value' => (!empty($current_takeda_consents_value['purpose_preference_boolean'])) ? $current_takeda_consents_value['purpose_preference_boolean'] : 0,
            ];

            $form['takeda_consents_fieldset']['takeda_consents_purposes'][$i]['preferences'] = [
                '#type' => 'hidden',
                '#value' => [],
            ];

            $form['takeda_consents_fieldset']['takeda_consents_purposes'][$i]['actions']['remove_group'] = [
                '#id' => $i,
                '#name' => 'button_remove_' . $i,
                '#type' => 'submit',
                '#value' => 'Remove group',
                '#submit' => ['::removeGroup'],
                '#limit_validation_errors' => [],
                '#ajax' => [
                    'callback' => '::fieldsetCallback',
                    'wrapper' => 'takeda-consents-fieldset-wrapper',
                ]
            ];
        }
        // End of the if for the all fieldset generation

        // Add button with the AJAX API implementation for the fieldeset
        $form['takeda_consents_fieldset']['actions']['add_fieldset'] = [
            '#type' => 'submit',
            '#value' => 'Add one group',
            '#submit' => ['::addOneGroup'],
            '#limit_validation_errors' => [],
            '#ajax' => [
                'callback' => '::fieldsetCallback',
                'wrapper' => 'takeda-consents-fieldset-wrapper',
            ],
        ];

        // Default form actions
        $form['actions'] = [
            '#type' => 'actions',
        ];

        // Probably removes some type of cache
        $form_state->setCached(FALSE);

        // The default submit form method
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => 'Submit'
        ];

        return $form;
    }

    // Adds one more group to the fieldset list
    public function addOneGroup(array &$form, FormStateInterface $form_state)
    {
        $temporary_field_data = [
            'request_info' => '',
            'purpose_id' => '',
            'purpose_name' => '',
            'purpose_preference_boolean' => 0,
            'preferences' => [
                [
                    'preference_fieldset' => [
                        'preference_id' => '',
                        'preference_name' => '',
                        'preference_options' => [
                            [
                                'option_fieldset' => [
                                    'option_id' => '', 
                                    'option_name' => ''
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $temporary_fields = $form_state->get('takeda_consents_temporary_fields');
        $temporary_fields[] = $temporary_field_data;
        $form_state->set('takeda_consents_temporary_fields', $temporary_fields);
        $form_state->setRebuild();
    }

    // Remove a group on it's remove button click
    public function removeGroup(array &$form, FormStateInterface $form_state)
    {
        $group_id = $form_state->getTriggeringElement()['#id'];
        $temporary_fields = $form_state->get('takeda_consents_temporary_fields_to_remove') ?: [];

        array_push($temporary_fields, $group_id);
        $form_state->set('takeda_consents_temporary_fields_to_remove', $temporary_fields);
        $form_state->setRebuild();
    }

    // The callback function to be executed with AJAX on add button click
    public function fieldsetCallback(array &$form, FormStateInterface $form_state)
    {
        return $form['takeda_consents_fieldset'];
    }

    // The default form submit function
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Get the values from the submitted form data
        $values = $form_state->getValue(['takeda_consents_fieldset']);
        unset($values['actions']);

        $config = \Drupal::service('config.factory')->getEditable('takeda_consents.settings');
        $takeda_consents_values = $config->get('takeda_consents_values') ?: []; // Takeda consents values check - blank array if no data so that it can be countable
        $takeda_consents_purposes = isset($takeda_consents_values['takeda_consents_purposes']) ? $takeda_consents_values['takeda_consents_purposes'] : []; // Takeda consents purposes values check - blank array if no data so that it can be countable
        $takeda_consents_language_values = $config->get('takeda_consents_language_values') ?: []; // Takeda consents language values check - blank array if no data so that it can be countable

        // Add the saved preferences from the other form to preserve them
        foreach ($takeda_consents_purposes as $current_purpose_key => $current_purpose_value) {
            // Skip to be deleted fields and remove the if the takeda_consents_language module data exists
            if (!empty($form_state->get('takeda_consents_temporary_fields_to_remove')) && in_array($current_purpose_key, $form_state->get('takeda_consents_temporary_fields_to_remove'))) {
                // If the purpose exists in the array, remove it
                if ($takeda_consents_language_values && isset($takeda_consents_language_values[$current_purpose_key])) {
                    unset($takeda_consents_language_values[$current_purpose_key]);
                    $config->set('takeda_consents_language_values', array_values($takeda_consents_language_values)); // array_values reindexes the array
                }

                // Skip the further functionality for the current loop
                continue;
            }

            if (isset($takeda_consents_purposes[$current_purpose_key]['preferences'])) {
                $values['takeda_consents_purposes'][$current_purpose_key]['preferences'] = $takeda_consents_purposes[$current_purpose_key]['preferences'];
            }
        }
        
        if (isset($values['takeda_consents_purposes'])) {
            $values['takeda_consents_purposes'] = array_values($values['takeda_consents_purposes']); // array_values reindexes the array
        }

        $config->set('takeda_consents_values', $values);
        $config->save();
        $form_state->set('takeda_consents_temporary_fields', []);
        $form_state->set('takeda_consents_temporary_fields_to_remove', []);
        return parent::submitForm($form, $form_state);
    }
}