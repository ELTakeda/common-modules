<?php

namespace Drupal\takeda_alchemer\Service;

use Drupal\Core\Form\FormStateInterface;
use Drupal\takeda_alchemer\AlchemerFieldsConfig as Config;

class FormBuilder extends Config
{ 
  
    /**
     * Build alchemer popup survey section
     * @param mixed $form
     * @param FormStateInterface $form_state
     * @param array $defaults
     */
    public function alchemerFormBuilder(&$form, FormStateInterface $form_state) {
        $node = $form_state->getFormObject()->getEntity();
        $popup_survey_type = self::POPUP_SURVEY_TYPE;

        $defaults = \Drupal::service('takeda_alchemer.helper')->retrieveSurveyData($node, $popup_survey_type);
        $form_state->set('popup_old_node_version', $defaults);

        $therapy_area_count = self::THERAPY_AREA_NUM;
        $website_product_count = self::WEBSITE_PRODUCT_NUM;

        //defaults
        $tags = !empty($defaults['popup_tags']) ? json_decode($defaults['popup_tags'], true) : [];
        $therapy_areas = !empty($defaults['popup_therapy_areas']) ? json_decode($defaults['popup_therapy_areas'], true) : [];
        $products = !empty($defaults['popup_products']) ? json_decode($defaults['popup_products'], true) : [];
        $function = !empty($defaults['popup_function']) ? $defaults['popup_function'] : '';

        //when no alchemer selfuration saved in db yet, set default values for form fields.
        if (!$tags) {
            $tags = self::popupDefaultTagValues();
        }

        $form['alchemer-popup-menu'] = [
            '#type' => 'details',
            '#title' => t('Alchemer Popup'),
            '#group' => 'advanced',
            '#tree' => TRUE,
            '#weight' => 3,
        ];

        $form['alchemer-popup-menu']['alchemer-popup-enabled'] = [
            '#type' => 'checkbox',
            '#title' => t('Turn on pop-up survey for this page'),
            '#default_value' => (isset($defaults['is_popup_active']) ? $defaults['is_popup_active'] : 0),
        ];

        $form['alchemer-popup-menu']['alchemer-popup-tags'] = [
            '#type' => 'container',
            '#array_parents' => ['alchemer-popup-menu'],
            '#states' => [
                'invisible' => [
                    'input[name="alchemer-popup-menu[alchemer-popup-enabled]"]' => ['checked' => FALSE],
                ],
            ],
        ];
        //field for country
        $form['alchemer-popup-menu']['alchemer-popup-tags'][self::FIELD_ALCHEMER_COUNTRY_NAME['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_COUNTRY_NAME['label']),
            '#default_value' => $tags[self::FIELD_ALCHEMER_COUNTRY_NAME['name']],
        ];

        //field for website name
        $form['alchemer-popup-menu']['alchemer-popup-tags'][self::FIELD_ALCHEMER_WEBSITE_NAME['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_WEBSITE_NAME['label']),
            '#default_value' =>  $tags[self::FIELD_ALCHEMER_WEBSITE_NAME['name']]
        ];

        //field for therapy area
        $form['alchemer-popup-menu']['alchemer-popup-tags'][self::FIELD_ALCHEMER_THERAPY_AREA['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_THERAPY_AREA['label']),
            '#default_value' =>  $tags[self::FIELD_ALCHEMER_THERAPY_AREA['name']],
        ];

        $form['alchemer-popup-menu']['alchemer-popup-tags']['therapy-area-menu'] = [
            '#type' => 'container',
            '#array_parents' => ['alchemer-popup-menu', 'alchemer-popup-tags'],
            '#states' => [
                'invisible' => [
                    'input[name="alchemer-popup-menu[alchemer-popup-tags]['.self::FIELD_ALCHEMER_THERAPY_AREA['name'].']"]' => ['checked' => FALSE],
                ],
            ],
        ];

        //text fields for therapy area when enabled
        for ($i = 1; $i <= $therapy_area_count; $i++) {
            $therapy_area_name = self::FIELD_ALCHEMER_THERAPY_AREA['name'] . '-' . $i;
            $form['alchemer-popup-menu']['alchemer-popup-tags']['therapy-area-menu'][$therapy_area_name] = [
                '#type' => 'textfield',
                '#element_validate' => ['takeda_alchemer_validate_field'],
                '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
                '#autocomplete_route_parameters' => array('field_name' => self::FIELD_ALCHEMER_THERAPY_AREA['name']),
                '#default_value' => (isset($therapy_areas[$therapy_area_name]) ? $therapy_areas[$therapy_area_name] : ''),

            ];
        }

        //field for product of website
        $form['alchemer-popup-menu']['alchemer-popup-tags'][self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_WEBSITE_PRODUCT['label']),
            '#default_value' => $tags[self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name']],
        ];

        $form['alchemer-popup-menu']['alchemer-popup-tags']['website-product-menu'] = [
            '#type' => 'container',
            '#array_parents' => ['alchemer-popup-menu', 'alchemer-popup-tags'],
            '#states' => [
                'invisible' => [
                    'input[name="alchemer-popup-menu[alchemer-popup-tags][' . self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name'] . ']"]' => ['checked' => FALSE],
                ],
            ],
        ];

        for ($i = 1; $i <= $website_product_count; $i++) {
            $website_product_name = self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name'] . '-' . $i;
            $form['alchemer-popup-menu']['alchemer-popup-tags']['website-product-menu'][$website_product_name] = [
                '#type' => 'textfield',
                '#element_validate' => ['takeda_alchemer_validate_field'],
                '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
                '#autocomplete_route_parameters' => array('field_name' => self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name']),
                '#default_value' => (isset($products[$website_product_name]) ? $products[$website_product_name] : ''),
            ];
        }

        //fields for HCP ID
        $form['alchemer-popup-menu']['alchemer-popup-tags'][self::FIELD_ALCHEMER_HCP_ID['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_HCP_ID['label']),
            '#default_value' => $tags[self::FIELD_ALCHEMER_HCP_ID['name']],
        ];

        $form['alchemer-popup-menu']['alchemer-popup-tags'][self::FIELD_ALCHEMER_FUNCTION['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_FUNCTION['label']),
            '#default_value' => $tags[self::FIELD_ALCHEMER_FUNCTION['name']],
        ];

    
        $form['alchemer-popup-menu']['alchemer-popup-tags']['function-menu'] = [
            '#type' => 'container',
            '#array_parents' => ['alchemer-popup-menu', 'alchemer-popup-tags'],
            '#states' => [
                'invisible' => [
                    'input[name="alchemer-popup-menu[alchemer-popup-tags]['.self::FIELD_ALCHEMER_FUNCTION['name'].']"]' => ['checked' => FALSE],
                ],
            ],
        ];

        //text input for function
        $form['alchemer-popup-menu']['alchemer-popup-tags']['function-menu'][0] = [
            '#type' => 'textfield',
            '#element_validate' => ['takeda_alchemer_validate_field'],
            '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
            '#autocomplete_route_parameters' => array('field_name' => self::FIELD_ALCHEMER_FUNCTION['name']),
            '#default_value' => $function,
        ];

        return $form;
    }

    /**
     * Build alchemer microfeedback survey section
     * @param mixed $form
     * @param FormStateInterface $form_state
     * @param array $defaults
     */
    public function microfeedbackFormBuilder(&$form, FormStateInterface $form_state) {
        $node = $form_state->getFormObject()->getEntity();
        $microfeedback_survey_type = self::MICROFEEDBACK_SURVEY_TYPE;

        $defaults = \Drupal::service('takeda_alchemer.helper')->retrieveSurveyData($node, $microfeedback_survey_type);
        $form_state->set('microfeedback_old_node_version', $defaults);

        $therapy_area_count = self::M_THERAPY_AREA_NUM;
        $website_product_count = self::M_WEBSITE_PRODUCT_NUM;

         //defaults
         $tags = !empty($defaults['microfeedback_tags']) ? json_decode($defaults['microfeedback_tags'], true) : [];
         $therapy_areas = !empty($defaults['microfeedback_therapy_areas']) ? json_decode($defaults['microfeedback_therapy_areas'], true) : [];
         $products = !empty($defaults['microfeedback_products']) ? json_decode($defaults['microfeedback_products'], true) : [];
         $function = !empty($defaults['microfeedback_function']) ? $defaults['microfeedback_function'] : '';
         $section = !empty($defaults['microfeedback_section']) ? $defaults['microfeedback_section'] : '';
 
         //when no alchemer selfuration saved in db yet, set default values for form fields.
         if (!$tags) {
             $tags = self::microfeedbackDefaultTagValues();
         }
 

        //define microfeedback section
        $form['alchemer-microfeedback-menu'] = [
            '#type' => 'details',
            '#title' => t('Alchemer Microfeedback'),
            '#group' => 'advanced',
            '#tree' => TRUE,
            '#weight' => 3,
        ];

        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-enabled'] = [
            '#type' => 'checkbox',
            '#title' => t('Turn on microfeedback survey for this page'),
            '#default_value' => (isset($defaults['is_microfeedback_active']) ? $defaults['is_microfeedback_active'] : 0),
        ];

        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags'] = [
            '#type' => 'container',
            '#array_parents' => ['alchemer-microfeedback-menu'],
            '#states' => [
                'invisible' => [
                    'input[name="alchemer-microfeedback-menu[alchemer-microfeedback-enabled]"]' => ['checked' => FALSE],
                ],
            ],
        ];

        //field for country
        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags'][self::FIELD_ALCHEMER_COUNTRY_NAME['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_COUNTRY_NAME['label']),
            '#default_value' => $tags[self::FIELD_ALCHEMER_COUNTRY_NAME['name']],
        ];

        //field for website name
        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags'][self::FIELD_ALCHEMER_WEBSITE_NAME['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_WEBSITE_NAME['label']),
            '#default_value' => $tags[self::FIELD_ALCHEMER_WEBSITE_NAME['name']],
        ];

        //field for therapy area
        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags'][self::FIELD_ALCHEMER_THERAPY_AREA['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_THERAPY_AREA['label']),
            '#default_value' => $tags[self::FIELD_ALCHEMER_THERAPY_AREA['name']],
        ];

        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags']['therapy-area-menu'] = [
            '#type' => 'container',
            '#array_parents' => ['alchemer-microfeedback-menu', 'alchemer-microfeedback-tags'],
            '#states' => [
                'invisible' => [
                    'input[name="alchemer-microfeedback-menu[alchemer-microfeedback-tags][' . self::FIELD_ALCHEMER_THERAPY_AREA['name'] . ']"]' => ['checked' => FALSE],
                ],
            ],
        ];

        //text fields for therapy area when enabled
        for ($i = 1; $i <= $therapy_area_count; $i++) {
            $therapy_area_name = self::FIELD_ALCHEMER_THERAPY_AREA['name'] . '-' . $i;
            $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags']['therapy-area-menu'][$therapy_area_name] = [
                '#type' => 'textfield',
                '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
                '#autocomplete_route_parameters' => array('field_name' => self::FIELD_ALCHEMER_THERAPY_AREA['name']),
                '#element_validate' => ['takeda_alchemer_validate_field'],
                '#default_value' => (isset($therapy_areas[$therapy_area_name]) ? $therapy_areas[$therapy_area_name] : ''),

            ];
        }

        //field for product of website
        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags'][self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_WEBSITE_PRODUCT['label']),
            '#default_value' => $tags[self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name']],
        ];

        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags']['website-product-menu'] = [
            '#type' => 'container',
            '#array_parents' => ['alchemer-microfeedback-menu', 'alchemer-microfeedback-tags'],
            '#states' => [
                'invisible' => [
                    'input[name="alchemer-microfeedback-menu[alchemer-microfeedback-tags][' . self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name'] . ']"]' => ['checked' => FALSE],
                ],
            ],
        ];

        for ($i = 1; $i <= $website_product_count; $i++) {
            $website_product_name = self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name'] . '-' . $i;
            $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags']['website-product-menu'][$website_product_name] = [
                '#type' => 'textfield',
                '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
                '#element_validate' => ['takeda_alchemer_validate_field'],
                '#autocomplete_route_parameters' => array('field_name' => self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name']),
                '#default_value' => (isset($products[$website_product_name]) ? $products[$website_product_name] : ''),
            ];
        }

        //fields for HCP ID
        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags'][self::FIELD_ALCHEMER_HCP_ID['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_HCP_ID['label']),
            '#default_value' => $tags[self::FIELD_ALCHEMER_HCP_ID['name']],
        ];

        //section
        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags'][self::FIELD_MICROFEEDBACK_SECTION['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_MICROFEEDBACK_SECTION['label']),
            '#default_value' => $tags[self::FIELD_MICROFEEDBACK_SECTION['name']],
        ];

        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags']['section-menu'] = [
            '#type' => 'container',
            '#array_parents' => ['alchemer-microfeedback-menu', 'alchemer-microfeedback-tags'],
            '#states' => [
                'invisible' => [
                    'input[name="alchemer-microfeedback-menu[alchemer-microfeedback-tags]['.self::FIELD_MICROFEEDBACK_SECTION['name'].']"]' => ['checked' => FALSE],
                ],
            ],
        ];

        //text input for section
        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags']['section-menu'][0] = [
            '#type' => 'textfield',
            '#element_validate' => ['takeda_alchemer_validate_field'],
            '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
            '#autocomplete_route_parameters' => array('field_name' => self::FIELD_MICROFEEDBACK_SECTION['name']),
            '#default_value' => $section,
        ];

        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags'][self::FIELD_MICROFEEDBACK_URL['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_MICROFEEDBACK_URL['label']),
            '#default_value' => $tags[self::FIELD_MICROFEEDBACK_URL['name']],
        ];

        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags'][self::FIELD_ALCHEMER_FUNCTION['name']] = [
            '#type' => 'checkbox',
            '#title' => t(self::FIELD_ALCHEMER_FUNCTION['label']),
            '#default_value' => $tags[self::FIELD_ALCHEMER_FUNCTION['name']],
        ];

        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags']['function-menu'] = [
            '#type' => 'container',
            '#array_parents' => ['alchemer-microfeedback-menu', 'alchemer-microfeedback-tags'],
            '#states' => [
                'invisible' => [
                    'input[name="alchemer-microfeedback-menu[alchemer-microfeedback-tags]['.self::FIELD_ALCHEMER_FUNCTION['name'].']"]' => ['checked' => FALSE],
                ],
            ],
        ];

        //text input for function
        $form['alchemer-microfeedback-menu']['alchemer-microfeedback-tags']['function-menu'][0] = [
            '#type' => 'textfield',
            '#element_validate' => ['takeda_alchemer_validate_field'],
            '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
            '#autocomplete_route_parameters' => array('field_name' => self::FIELD_ALCHEMER_FUNCTION['name']),
            '#default_value' => $function,
        ];

        return $form;
    }

    /**
     * Form submission logic for both alchemer sections - popup and microfeedback
     * @param FormStateInterface $form_state
     * @param string $type
     * 
     * @return void
     */
    public function submitForm(FormStateInterface &$form_state, $type) {
        
        $table = self::getSurveyTableByType($type);

        $nid = $form_state->getValue('nid');
        
        $values = $form_state->getValue('alchemer-'.$type.'-menu');
        $therapy_area_values = json_encode($values['alchemer-'.$type.'-tags']['therapy-area-menu']);
        $product_values = json_encode($values['alchemer-'.$type.'-tags']['website-product-menu']);
        $function_value = $values['alchemer-'.$type.'-tags']['function-menu'][0];


        //unset menus from tags array
        unset($values['alchemer-'.$type.'-tags']['therapy-area-menu']);
        unset($values['alchemer-'.$type.'-tags']['website-product-menu']);
        unset($values['alchemer-'.$type.'-tags']['function-menu']);

        //get section value
        if ($type === self::MICROFEEDBACK_SURVEY_TYPE) {
            $section_value = $values['alchemer-microfeedback-tags']['section-menu'][0];
            unset($values['alchemer-microfeedback-tags']['section-menu']);
        }
        //Update group id on edit action
        $group_id = \Drupal::service('takeda_alchemer.tags_helper')->updateGroupId($form_state, $type);
    
        //create json object with tags to pass to db
        $tags = json_encode($values['alchemer-'.$type.'-tags']);

        $connection = \Drupal::database();

        //define fields
        $fields = [];
        $fields['nid'] = $nid;
        if ($table === self::ALCHEMER_POPUP_TABLE) {
           //create array with fields and their values to insert
           $fields['is_popup_active'] = $values['alchemer-popup-enabled'];
           $fields['popup_tags'] = $tags;
           $fields['popup_therapy_areas'] = $therapy_area_values;
           $fields['popup_products'] = $product_values;
           $fields['popup_function'] = $function_value;
           $fields['popup_group_id'] = $group_id;  
        }

        else {
           //create array with fields and their values to insert
           $fields['is_microfeedback_active'] = $values['alchemer-microfeedback-enabled'];
           $fields['microfeedback_tags'] = $tags;
           $fields['microfeedback_therapy_areas'] = $therapy_area_values;
           $fields['microfeedback_products'] = $product_values;
           $fields['microfeedback_function'] = $function_value;
           $fields['microfeedback_section'] = $section_value;
           $fields['microfeedback_group_id'] = $group_id;
        }

        //We use upsert query to update the record if exist or insert new one
        $upsert = $connection->upsert($table)
        ->fields(array_keys($fields))
        ->key('nid');
        $upsert->values($fields);
        $upsert->execute();            
    }
}