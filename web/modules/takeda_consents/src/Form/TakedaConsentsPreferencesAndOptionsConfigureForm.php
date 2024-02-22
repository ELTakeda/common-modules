<?php

namespace Drupal\takeda_consents\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * TakedaConsentsPreferencesAndOptionsConfigureForm
 * Plugin configuration form.
 */
class TakedaConsentsPreferencesAndOptionsConfigureForm extends ConfigFormBase
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
        $takeda_consents_purposes = isset($takeda_consents_values['takeda_consents_purposes']) ? $takeda_consents_values['takeda_consents_purposes'] : []; // Takeda consents purposes values check - blank array if no data so that it can be countable
        $temporary_preference_fields = $form_state->get('takeda_consents_temporary_preference_fields');
        $temporary_preference_fields_to_delete = $form_state->get('takeda_consents_temporary_preference_fields_to_remove');
        $temporary_option_fields = $form_state->get('takeda_consents_temporary_option_fields');
        $temporary_option_fields_to_delete = $form_state->get('takeda_consents_temporary_option_fields_to_remove');

        // Create a list of all available purposes
        $select_value_array = [];

        for ($i = 0; $i < count($takeda_consents_purposes); $i++) {
            $current_takeda_consents_value = $takeda_consents_purposes[$i];

            // Skip the purpose if it doesn't have preferences and/or options enabled
            if (!$current_takeda_consents_value['purpose_preference_boolean']) {
                continue;
            }

            $current_name = $current_takeda_consents_value['purpose_name'];
            $select_value_array[$i] = $current_name; // Set the current name to the purpose array index
        }

        $form['purpose_select'] = [
            '#type' => 'select',
            '#title' => 'Select a purpose',
            '#empty_option' => '',
            '#options' => $select_value_array,
        ];

        // The general fieldset wrapper
        $form['takeda_consents_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => 'Preferences and Options',
            '#prefix' => '<div id="takeda-consents-fieldset-wrapper">',
            '#suffix' => '</div>',
        ];

        // Loop to display all of the purpose sections
        foreach ($takeda_consents_purposes as $current_purpose_key => $current_purpose_data) {
            $current_purpose_data = $takeda_consents_purposes[$current_purpose_key];

            // Skip the purpose if it doesn't have preferences and/or options enabled
            if (!$current_purpose_data['purpose_preference_boolean']) {
                continue;
            }

            $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'] = [
                '#type' => 'fieldset',
                '#title' => 'Preferences and options for - ' . $current_purpose_data['purpose_name'],
                '#prefix' => '<div id="takeda-consents-individual-fieldset-wrapper-' . $current_purpose_key . '">',
                '#suffix' => '</div>',
                '#states' => [ // the conditional field display functionality for the select
                    'visible' => [
                      ':input[name="purpose_select"]' => ['value' => $current_purpose_key],
                    ]
                ]
            ];
            
            $current_preferences = $current_purpose_data['preferences'];

            // Add new preference data to preferences if such exists
            if (!empty($temporary_preference_fields) && !empty($temporary_preference_fields[$current_purpose_key])) {
                $current_preferences = array_merge($current_preferences, $temporary_preference_fields[$current_purpose_key]);
            }

            // Loop to display all of the preferences for the purpose section
            foreach ($current_preferences as $current_preference_key => $current_preference_value) {
                // Remove preference data temporary functionality
                if (isset($temporary_preference_fields_to_delete) 
                    && isset($temporary_preference_fields_to_delete[$current_purpose_key]) 
                    && isset($temporary_preference_fields_to_delete[$current_purpose_key][$current_preference_key])
                ) {
                    continue;
                }

                $current_preference_data = $current_preferences[$current_preference_key]['preference_fieldset'];

                $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset'] = [
                    '#type' => 'fieldset',
                    '#title' => 'Individual preference',
                    '#prefix' => '<div id="takeda-consents-individual-preference-fieldset-wrapper-' . $current_preference_key . '">',
                    '#suffix' => '</div>',
                ];

                $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_id'] = [
                    '#type' => 'textfield',
                    '#title' => 'Consent Preference ID',
                    '#description' => 'The Preference ID of the consent that you want to add. May include "-", must not include spaces or other symbols',
                    '#default_value' => (!empty($current_preference_data['preference_id'])) ? $current_preference_data['preference_id'] : '',
                    '#required' => TRUE,
                ];
        
                $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_name'] = [
                    '#type' => 'textfield',
                    '#title' => 'Consent Preference Name',
                    '#description' => 'The Preference name of the consent name that you want to be displayed in some site configurations',
                    '#default_value' => (!empty($current_preference_data['preference_name'])) ? $current_preference_data['preference_name'] : '',
                    '#required' => TRUE,
                ];

                $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_hidden_boolean'] = [
                    '#type' => 'checkbox',
                    '#title' => 'The preference and options should be present but hidden in the form',
                    '#default_value' => (!empty($current_preference_data['preference_hidden_boolean'])) ? $current_preference_data['preference_hidden_boolean'] : 0,
                ];
                        
                // The options fieldset
                $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options'] = [
                    '#type' => 'fieldset',
                    '#title' => 'The options for the specific preference',
                    '#prefix' => '<div id="takeda-consents-options-fieldset-wrapper-' . $current_purpose_key . '-' . $current_preference_key . '">',
                    '#suffix' => '</div>',
                ];

                // The preferences remove button - move it before the options fieldset to render the button before it
                $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['actions']['remove_preference'] = [
                    '#id' => 'preferences_remove_' . $current_purpose_key . '-' . $current_preference_key,
                    '#name' => 'preferences_remove_' . $current_purpose_key . '-' . $current_preference_key,
                    '#type' => 'submit',
                    '#value' => 'Remove preference',
                    '#submit' => ['::removePreference'],
                    '#limit_validation_errors' => [],
                    '#ajax' => [
                        'callback' => '::preferenceCallback',
                        'wrapper' => 'takeda-consents-individual-fieldset-wrapper-' . $current_purpose_key
                    ]
                ];

                $current_options = $current_preference_data['preference_options'];
                
                if (!empty($temporary_option_fields)
                    && !empty($temporary_option_fields[$current_purpose_key])
                    && !empty($temporary_option_fields[$current_purpose_key][$current_preference_key])
                ) {
                    $current_options = array_merge($current_options, $temporary_option_fields[$current_purpose_key][$current_preference_key]);
                }

                // Loop to display all of the options for the preference
                foreach ($current_options as $current_option_key => $current_option_value) {
                    // Remove option data temporary functionality
                    if (isset($temporary_option_fields_to_delete)
                        && isset($temporary_option_fields_to_delete[$current_purpose_key])
                        && isset($temporary_option_fields_to_delete[$current_purpose_key][$current_preference_key])
                        && isset($temporary_option_fields_to_delete[$current_purpose_key][$current_preference_key][$current_option_key])
                    ) {
                        continue;
                    }

                    $current_option_data = $current_options[$current_option_key]['option_fieldset'];

                    $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options'][$current_option_key]['option_fieldset'] = [
                        '#type' => 'fieldset',
                        '#title' => 'An option group',
                        '#prefix' => '<div id="takeda-consents-individual-options-fieldset-wrapper-' . $current_option_key . '">',
                        '#suffix' => '</div>',
                    ];

                    $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options'][$current_option_key]['option_fieldset']['option_id'] = [
                        '#type' => 'textfield',
                        '#title' => 'Option ID',
                        '#description' => 'The Option ID used in OneTrust. May include "-", must not include spaces or other symbols',
                        '#default_value' => (!empty($current_option_data['option_id'])) ? $current_option_data['option_id'] : '',
                        '#required' => TRUE,
                    ];
        
                    $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options'][$current_option_key]['option_fieldset']['option_name'] = [
                        '#type' => 'textfield',
                        '#title' => 'Option Name',
                        '#description' => 'The Option name displayed to the user',
                        '#default_value' => (!empty($current_option_data['option_name'])) ? $current_option_data['option_name'] : '',
                        '#required' => TRUE,
                    ];

                    $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options'][$current_option_key]['option_fieldset']['option_weight'] = [
                        '#type' => 'textfield',
                        '#title' => 'Option Weight',
                        '#description' => 'The Option Weight is used to define how the options are displayed. Allowe values range from 1 (top) to 100 (bottom)',
                        '#default_value' => (!empty($current_option_data['option_weight'])) ? $current_option_data['option_weight'] : '',
                        '#required' => TRUE,
                    ];

                    // The option remove button
                    $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options'][$current_option_key]['option_fieldset']['actions']['remove_option'] = [
                        '#id' => 'options_remove_' . $current_purpose_key . '_' . $current_preference_key . '_' . $current_option_key,
                        '#name' => 'options_remove_' . $current_purpose_key . '_' . $current_preference_key . '_' . $current_option_key,
                        '#type' => 'submit',
                        '#value' => 'Remove option',
                        '#submit' => ['::removeOption'],
                        '#limit_validation_errors' => [],
                        '#ajax' => [
                            'callback' => '::optionCallback',
                            'wrapper' => 'takeda-consents-options-fieldset-wrapper-' . $current_purpose_key . '-' . $current_preference_key
                        ]
                    ];
                }

                // Add button with the AJAX API implementation for the options fieldset
                $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options']['actions']['add_option'] = [
                    '#id' => 'options_add_' . $current_purpose_key . '_' . $current_preference_key,
                    '#name' => 'options_add_' . $current_purpose_key . '_' . $current_preference_key,
                    '#type' => 'submit',
                    '#value' => 'Add option',
                    '#submit' => ['::addOneOption'],
                    '#limit_validation_errors' => [],
                    '#ajax' => [
                        'callback' => '::optionCallback',
                        'wrapper' => 'takeda-consents-options-fieldset-wrapper-'. $current_purpose_key . '-' . $current_preference_key
                    ],
                ];
            }

            // Add button with the AJAX API implementation for the preferences fieldset
            $form['takeda_consents_fieldset'][$current_purpose_key]['preferences_and_options']['actions']['add_preference'] = [
                '#id' => 'preferences_add_' . $current_purpose_key,
                '#name' => 'preferences_add_' . $current_purpose_key,
                '#type' => 'submit',
                '#value' => 'Add preference',
                '#submit' => ['::addOnePreference'],
                '#limit_validation_errors' => [],
                '#ajax' => [
                    'callback' => '::preferenceCallback',
                    'wrapper' => 'takeda-consents-individual-fieldset-wrapper-'. $current_purpose_key
                ],
            ];
        }

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

    // Adds one more preference fieldset to the preference fieldset list
    public function addOnePreference(array &$form, FormStateInterface $form_state)
    {
        $purpose_id = $form_state->getTriggeringElement()['#id'];

        if (preg_match("/preferences_add_/i", $purpose_id)) {
            $purpose_id = str_replace('preferences_add_', '', $purpose_id);
        }

        $temporary_field_data = [
            'preference_fieldset' => [
                'preference_id' => '',
                'preference_name' => '',
                'preference_options' => [
                    [
                        'option_fieldset' => [
                            'option_id' => '', 
                            'option_name' => '',
                            'option_weight' => '',
                        ]
                    ]
                ]
            ]
        ];

        $temporary_fields = $form_state->get('takeda_consents_temporary_preference_fields');
        $temporary_fields[$purpose_id][] = $temporary_field_data;
        $form_state->set('takeda_consents_temporary_preference_fields', $temporary_fields);
        $form_state->setRebuild();
    }

    // Remove a preference on it's remove button click
    public function removePreference(array &$form, FormStateInterface $form_state)
    {
        $data_ids = $form_state->getTriggeringElement()['#id'];

        if (preg_match("/preferences_remove_/i", $data_ids)) {
            $data_ids = str_replace('preferences_remove_', '', $data_ids);
        }

        $data_ids_split = explode('-', $data_ids);
        $purpose_id = $data_ids_split[0];
        $preference_id = $data_ids_split[1];

        $temporary_preference_fields_to_delete = $form_state->get('takeda_consents_temporary_preference_fields_to_remove') ?: [];

        if (empty($temporary_preference_fields_to_delete[$purpose_id])) {
            $temporary_preference_fields_to_delete[$purpose_id] = [];
        }

        $temporary_preference_fields_to_delete[$purpose_id][$preference_id] = '';
        $form_state->set('takeda_consents_temporary_preference_fields_to_remove', $temporary_preference_fields_to_delete);
        $form_state->setRebuild();
    }

    // The callback function to be executed with AJAX on add button click
    public function preferenceCallback(array &$form, FormStateInterface $form_state)
    {
        $field_id = $form_state->getTriggeringElement()['#id'];

        if (preg_match("/preferences_add_/i", $field_id)) {
            $field_id = str_replace('preferences_add_', '', $field_id);
        } elseif (preg_match("/preferences_remove_/i", $field_id)) {
            $field_id = str_replace('preferences_remove_', '', $field_id);
            $data_ids_split = explode('-', $field_id);
            $purpose_id = $data_ids_split[0];
            $field_id = $purpose_id;
        }

        return $form['takeda_consents_fieldset'][$field_id]['preferences_and_options'];
    }

    // Adds one more option fieldset to the option fieldset list
    public function addOneOption(array &$form, FormStateInterface $form_state)
    {
        $data_ids = $form_state->getTriggeringElement()['#id'];
        
        if (preg_match("/options_add_/i", $data_ids)) {
            $data_ids = str_replace('options_add_', '', $data_ids);
        }
        
        $data_ids_split = explode('_', $data_ids);
        $purpose_id = $data_ids_split[0];
        $preference_id = $data_ids_split[1];

        $new_option_data = [
            'option_fieldset' => [
                'option_id' => '', 
                'option_name' => '',
                'option_weight' => ''
            ]
        ];

        $temporary_fields = $form_state->get('takeda_consents_temporary_option_fields');
        $temporary_fields[$purpose_id][$preference_id][] = $new_option_data;
        $form_state->set('takeda_consents_temporary_option_fields', $temporary_fields);
        $form_state->setRebuild();
    }

    // Remove an option on it's remove button click
    public function removeOption(array &$form, FormStateInterface $form_state)
    {
        $data_ids = $form_state->getTriggeringElement()['#id'];

        if (preg_match("/options_remove_/i", $data_ids)) {
            $data_ids = str_replace('options_remove_', '', $data_ids);
        }

        $data_ids_split = explode('_', $data_ids);
        $purpose_id = $data_ids_split[0];
        $preference_id = $data_ids_split[1];
        $option_id = $data_ids_split[2];

        $temporary_options_to_delete = $form_state->get('takeda_consents_temporary_option_fields_to_remove') ?: [];

        if (empty($temporary_options_to_delete[$purpose_id])) {
            $temporary_options_to_delete[$purpose_id] = [];
        }

        if (empty($temporary_options_to_delete[$purpose_id][$preference_id])) {
            $temporary_options_to_delete[$purpose_id][$preference_id] = [];
        }

        $temporary_options_to_delete[$purpose_id][$preference_id][$option_id] = '';
        $form_state->set('takeda_consents_temporary_option_fields_to_remove', $temporary_options_to_delete);
        $form_state->setRebuild();
    }

    // The callback function to be executed with AJAX on add button click
    public function optionCallback(array &$form, FormStateInterface $form_state)
    {
        $data_ids = $form_state->getTriggeringElement()['#id'];
        
        if (preg_match("/options_add_/i", $data_ids)) {
            $data_ids = str_replace('options_add_', '', $data_ids);
        } elseif (preg_match("/options_remove_/i", $data_ids)) {
            $data_ids = str_replace('options_remove_', '', $data_ids);
        }

        $data_ids_split = explode('_', $data_ids);
        $purpose_id = $data_ids_split[0];
        $preference_id = $data_ids_split[1];

        return $form['takeda_consents_fieldset'][$purpose_id]['preferences_and_options'][$preference_id]['preference_fieldset']['preference_options'];
    }

    public function sortOptionsByWeight($a, $b) {
        return $a['option_fieldset']['option_weight'] <=> $b['option_fieldset']['option_weight'];
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
        $temporary_preference_fields_to_delete = $form_state->get('takeda_consents_temporary_preference_fields_to_remove');
        $temporary_option_fields_to_delete = $form_state->get('takeda_consents_temporary_option_fields_to_remove');

        // Loop each purpose
        foreach ($takeda_consents_purposes as $current_purpose_key => $current_purpose_value) {
            // Skip the purpose if it doesn't have preferences and/or options enabled
            if (!$takeda_consents_purposes[$current_purpose_key]['purpose_preference_boolean']) {
                continue;
            }

            // Remove the actions for each purpose
            unset($values[$current_purpose_key]['preferences_and_options']['actions']);

            // Loop each existing preference from the saved data
            foreach ($current_purpose_value['preferences'] as $current_preference_key => $current_preference_value) {
                // Skip to be deleted preferences
                if (isset($temporary_preference_fields_to_delete) 
                    && isset($temporary_preference_fields_to_delete[$current_purpose_key]) 
                    && isset($temporary_preference_fields_to_delete[$current_purpose_key][$current_preference_key])
                ) {
                    // If the preference exists in the array, remove it for each language
                    if ($takeda_consents_language_values 
                        && isset($takeda_consents_language_values[$current_purpose_key])
                    ) {
                        foreach ($takeda_consents_language_values[$current_purpose_key] as $current_language_preference_key => $current_language_value) {
                            if (isset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key])
                                && isset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key][$current_preference_key])
                            ) {
                                unset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key][$current_preference_key]);
                            }
                        }

                        $config->set('takeda_consents_language_values', $takeda_consents_language_values); // array_values reindexes the array
                    }

                    continue;
                }

                // Remove the actions for each preference- used for the input state of the module
                unset($values[$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['actions']);
                unset($values[$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options']['actions']);

                // Create a new options array with the option IDs
                $new_options = [];

                // Loop each option
                $current_options = $current_preference_value['preference_fieldset']['preference_options'];

                // Remove the deleted options from the language data
                foreach ($current_options as $current_option_key => $current_option_value) {
                    // Skip to be deleted options
                    if (isset($temporary_option_fields_to_delete)
                        && isset($temporary_option_fields_to_delete[$current_purpose_key])
                        && isset($temporary_option_fields_to_delete[$current_purpose_key][$current_preference_key])
                        && isset($temporary_option_fields_to_delete[$current_purpose_key][$current_preference_key][$current_option_key])
                    ) {
                        // If the option exists in the array, remove it for each language
                        if ($takeda_consents_language_values && isset($takeda_consents_language_values[$current_purpose_key])) {
                            foreach ($takeda_consents_language_values[$current_purpose_key] as $current_language_preference_key => $current_language_value) {
                                if (isset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key])
                                    && isset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key][$current_preference_key])
                                    && isset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key][$current_preference_key][$current_option_key])
                                ) {
                                    unset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key][$current_preference_key][$current_option_key]);
                                }
                            }
                            
                            $config->set('takeda_consents_language_values', $takeda_consents_language_values);
                        }

                        continue;
                    }
                }

                // Update all of the values with the new IDs of the option fields
                $current_values_for_purpose_and_preference = $values[$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options'];

                foreach ($current_values_for_purpose_and_preference as $current_option_key_from_values => $current_option_value_from_values) {
                    // Skip to be deleted options
                    if (isset($temporary_option_fields_to_delete)
                        && isset($temporary_option_fields_to_delete[$current_purpose_key])
                        && isset($temporary_option_fields_to_delete[$current_purpose_key][$current_preference_key])
                        && isset($temporary_option_fields_to_delete[$current_purpose_key][$current_preference_key][$current_option_key])
                    ) {
                        continue;
                    }

                    // Get the current option id
                    $current_option_id = $current_option_value_from_values['option_fieldset']['option_id'];

                    // If the ID is changed, remove the old ID and update it everywhere
                    if ($current_option_key_from_values !== $current_option_id) {
                        unset($values[$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options'][$current_option_key_from_values]);
                        
                        // Remove the old option id from the language array
                        if ($takeda_consents_language_values && isset($takeda_consents_language_values[$current_purpose_key])) {
                            foreach ($takeda_consents_language_values[$current_purpose_key] as $current_language_preference_key => $current_language_value) {
                                if (isset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key])
                                    && isset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key][$current_preference_key])
                                    && isset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key][$current_preference_key][$current_option_key])
                                ) {
                                    unset($takeda_consents_language_values[$current_purpose_key][$current_language_preference_key][$current_preference_key][$current_option_key]);
                                }
                            }
                            
                            $config->set('takeda_consents_language_values', $takeda_consents_language_values);
                        }
                    }

                    // Remove the actions from the option
                    unset($current_option_value_from_values['option_fieldset']['actions']);

                    // Set the new option
                    $new_options[$current_option_id] = $current_option_value_from_values;
                }

                // Sort the options array
                uasort($new_options, ['Drupal\takeda_consents\Form\TakedaConsentsPreferencesAndOptionsConfigureForm', 'sortOptionsByWeight']);

                // Set the new array
                $values[$current_purpose_key]['preferences_and_options'][$current_preference_key]['preference_fieldset']['preference_options'] = $new_options;
            }

            // Remove problematic action arrays
            foreach ($values[$current_purpose_key]['preferences_and_options'] as $values_preferences_key => $values_preferences_value) {
                // Remove the actions for each preference - used for the blank state of the module
                unset($values[$current_purpose_key]['preferences_and_options'][$values_preferences_key]['preference_fieldset']['actions']);
                unset($values[$current_purpose_key]['preferences_and_options'][$values_preferences_key]['preference_fieldset']['preference_options']['actions']);
            }

            // Set the option IDs if they were changed, but not updated
            // due to them not being part of an existing preference for the current purpose
            foreach ($values[$current_purpose_key]['preferences_and_options'] as $values_preferences_option_key => $values_preferences_option_value) {
                $values_current_options = $values_preferences_option_value['preference_fieldset']['preference_options'];

                foreach ($values_current_options as $values_option_key => $values_option_value) {
                    // Get the current option id
                    $current_option_id = $values_option_value['option_fieldset']['option_id'];

                    // If the ID is changed, remove the old ID and update it everywhere
                    if ($values_option_key !== $current_option_id) {
                        unset($values[$current_purpose_key]['preferences_and_options'][$values_preferences_option_key]['preference_fieldset']['preference_options'][$values_option_key]);
                        
                        $values[$current_purpose_key]['preferences_and_options'][$values_preferences_option_key]['preference_fieldset']['preference_options'][$current_option_id] = $values_option_value;
                    }
                }
            }

            $current_purpose_preference_data = $values[$current_purpose_key]['preferences_and_options'];
            $takeda_consents_purposes[$current_purpose_key]['preferences'] = $current_purpose_preference_data;
            $takeda_consents_values['takeda_consents_purposes'] = $takeda_consents_purposes;
        }

        $config->set('takeda_consents_values', $takeda_consents_values);
        $config->save();
        $form_state->set('takeda_consents_temporary_preference_fields', []);
        $form_state->set('takeda_consents_temporary_option_fields', []);
        $form_state->set('takeda_consents_temporary_preference_fields_to_remove', []);
        $form_state->set('takeda_consents_temporary_option_fields_to_remove', []);
        return parent::submitForm($form, $form_state);
    }
}