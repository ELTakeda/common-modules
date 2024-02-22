<?php

namespace Drupal\takeda_alchemer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\takeda_alchemer\AlchemerFieldsConfig as Config;

/**
 * class TakedaAlchemerMicrofeedback
 */
class TakedaAlchemerMicrofeedback extends ConfigFormBase
{

    /**
     * Determines the ID of a form.
     * @return string
     */
    public function getFormId()
    {
        return 'takeda_alchemer_feedback';
    }


    /**
     * Gets the configuration names that will be editable.
     * @return array
     */
    public function getEditableConfigNames()
    {
        return [
            'takeda_alchemer_feedback.settings'
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
        $config = $this->config('takeda_alchemer_feedback.settings');
        $microfeedback_values = $config->get('microfeedback_values');

        $temporary_fields = $form_state->get('microfeedback_temporary_fields') ?: [];
        $temporary_fields_to_remove = $form_state->get('microfeedback_temporary_fields_to_remove');

        $form['microfeedback_page_wrapper'] = [
            '#type' => 'fieldset',
            '#title' => 'Microfeedback tag groups',
            '#prefix' => '<div id="microfeedback-therapy-areas-products-tags">',
            '#suffix' => '</div>',
            '#tree' => true,
        ];

        $microfeedback_groups = isset($microfeedback_values['microfeedback_groups']) ? $microfeedback_values['microfeedback_groups'] : [];
        if (!empty($temporary_fields)) {
            $microfeedback_groups = array_merge($microfeedback_groups, $temporary_fields);
        }

        for ($m = 0; $m < count($microfeedback_groups); $m++) {

            $group_id = $m;

            $default_selected_content_types = $microfeedback_groups[$m]['apply_to_pages_fieldset']['content_types'];

            //get default values for specific group id
            $defaults = \Drupal::service('takeda_alchemer.tags_helper')->getAlchemerTagsByGroupId($group_id, CONFIG::ALCHEMER_MICROFEEDBACK_TABLE);

            //default values for select field
            $default_nids = [];
            if (!empty($defaults)) {
                foreach ($defaults as $default_value) {
                    $default_nids[] = $default_value['nid'];
                }
            }
            
            $default_therapy_areas = !empty($defaults) ? json_decode($defaults[0]['microfeedback_therapy_areas'], true) : [];
            $default_products = !empty($defaults) ? json_decode($defaults[0]['microfeedback_products'], true) : [];
            $function = !empty($defaults) ? $defaults[0]['microfeedback_function'] : [];
            $section = !empty($defaults) ? $defaults[0]['microfeedback_section'] : [];

            if (!empty($temporary_fields_to_remove) && in_array($m, $temporary_fields_to_remove)) {
                continue;
            }

            $form['microfeedback_page_wrapper']['microfeedback_groups'][$m] = [
                '#type' => 'fieldset',
                '#title' => '',
                '#prefix' => '<div id="microfeedback-groups">',
                '#suffix' => '</div>',
            ];

            $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['tags_section_fieldset'] = [
                '#type' => 'fieldset',
                '#title' => 'Tags',
                '#prefix' => '<div id="microfeedback-section-tags">',
                '#suffix' => '</div>',
            ];


            $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['tags_section_fieldset']['therapy_area_tags'] = [
                '#type' => 'container',
                '#prefix' => '<div id="M-TA-section-tags">',
                '#suffix' => '</div>',
                '#attributes' => ['style' => 'margin-bottom: 30px;'],
            ];

            //therapy area fields 
            for ($i = 1; $i <= Config::M_THERAPY_AREA_NUM; $i++) {
                $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['tags_section_fieldset']['therapy_area_tags']['therapy-area-' . $i] = [
                    '#id' => 'm_therapy_area_tags_' . $i . '',
                    '#type' => 'textfield',
                    '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
                    '#autocomplete_route_parameters' => array('field_name' => Config::FIELD_ALCHEMER_THERAPY_AREA['name']),
                    '#element_validate' => ['::validateTag'],
                    '#title' => $this->t('TA-' . $i . ''),
                    '#default_value' => (!empty($default_therapy_areas) ? $default_therapy_areas['therapy-area-'.$i] : ''),
                    '#size' => 60
                ];
            }

            $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['tags_section_fieldset']['product_tags'] = [
                '#type' => 'container',
                '#title' => 'Products',
                '#prefix' => '<div id="M-products-section-tags">',
                '#suffix' => '</div>',
                '#attributes' => ['style' => 'margin-bottom: 30px;'],
            ];

            //product fields
            for ($j = 1; $j <= Config::M_WEBSITE_PRODUCT_NUM; $j++) {
                $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['tags_section_fieldset']['product_tags']['website-product-' . $j] = [
                    '#id' => 'm_product_tags_' . $j . '',
                    '#type' => 'textfield',
                    '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
                    '#autocomplete_route_parameters' => array('field_name' => Config::FIELD_ALCHEMER_WEBSITE_PRODUCT['name']),
                    '#element_validate' => ['::validateTag'],
                    '#title' => $this->t('PR-' . $j . ''),
                    '#default_value' => (!empty($default_products) ? $default_products['website-product-'.$j] : ''),
                    '#size' => 70
                ];
            }

            $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['tags_section_fieldset']['function'] = [
                '#id' => 'function',
                '#type' => 'textfield',
                '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
                '#autocomplete_route_parameters' => array('field_name' => Config::FIELD_ALCHEMER_FUNCTION['name']),
                '#element_validate' => ['::validateTag'],
                '#title' => $this->t('Function'),
                '#default_value' => $function,
                '#size' => 60
            ];

            $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['tags_section_fieldset']['section'] = [
                '#id' => 'section',
                '#type' => 'textfield',
                '#autocomplete_route_name' => 'takeda_alchemer.autocomplete',
                '#autocomplete_route_parameters' => array('field_name' => Config::FIELD_MICROFEEDBACK_SECTION['name']),
                '#element_validate' => ['::validateTag'],
                '#title' => $this->t('Section'),
                '#default_value' => $section,
                '#size' => 60
            ];

            $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['apply_to_pages_fieldset'] = [
                '#type' => 'fieldset',
                '#title' => 'Pages to apply these tags to',
                '#prefix' => '<div id="apply-to-pages">',
                '#suffix' => '</div>',
            ];

            //Get all content types from DB
            $content_types = \Drupal::service('takeda_alchemer.tags_helper')->retrieveAllContentTypes();

            $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['apply_to_pages_fieldset']['content_types'] = [
                '#type' => 'select',
                '#ajax_data' => $m,
                '#required' => TRUE,
                '#title' => $this->t('Choose content types'),
                '#attributes' => [
                    'class' => ['choose-content-types'],
                    'style' => 'width:500px; height:300px;'
                ],
                '#ajax' => [
                    'callback' => [$this, 'fieldsetCallback'],
                    'wrapper' => 'microfeedback-therapy-areas-products-tags',
                    'event' => 'change',
                    'progress' => [
                        'type' => 'throbber',
                        'message' => $this->t('Verifying entry...'),
                    ],
                    'disable-refocus' => TRUE,
                ],
                '#multiple' => TRUE,
                '#options' => $content_types,
                '#default_value' => (!empty($default_selected_content_types) ? $default_selected_content_types : []),
            ];
            
            $selected_content_types = [];
            
            $values = $form_state->getValue(['microfeedback_page_wrapper']);

            if (!isset($values['microfeedback_groups'][$m]['apply_to_pages_fieldset']['content_types'])) {
                //Get the types directly from the groups
                $selected_content_types = $default_selected_content_types;
            }

            else {
                //Get selected types from the form state if the specific group is not yet saved in the database
                $selected_content_types = $values['microfeedback_groups'][$m]['apply_to_pages_fieldset']['content_types'];
            }

            //unset unexpected data
            if(isset($selected_content_types['option_id'])) unset($selected_content_types['option_id']);
            if(isset($selected_content_types['option_name'])) unset($selected_content_types['option_name']);
            
            //Extract nodes title options from the selected content types
            $options = \Drupal::service('takeda_alchemer.tags_helper')->retrieveAllNodesForSelect($selected_content_types);
            if ($selected_content_types) {
                $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['apply_to_pages_fieldset']['apply_to_pages'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Apply to'),
                    '#required' => TRUE,
                    '#description' => $this->t("Apply microfeedback to single or multiple pages"),
                    '#attributes' => [
                        'class' => ['microfeedback-apply-to-pages'],
                        'style' => 'width:500px; height:300px;'
                    ],
                    '#element_validate' => ['::checkNodeInGroup'],
                    '#multiple' => TRUE,
                    '#options' => (!empty($options) ? $options : []),
                    '#default_value' => array_unique($default_nids)
                ];
            }
            
            //remove group button
            $form['microfeedback_page_wrapper']['microfeedback_groups'][$m]['actions']['remove_group'] = [
                '#id' => $m,
                '#name' => 'button_remove_' . $m,
                '#type' => 'submit',
                '#value' => 'Remove group',
                '#submit' => ['::removeGroup'],
                '#limit_validation_errors' => [],
                '#ajax' => [
                    'callback' => '::fieldsetCallback',
                    'wrapper' => 'microfeedback-therapy-areas-products-tags',
                ]
            ];
        }

        // Add button with the AJAX API implementation for the fieldeset
        $form['microfeedback_page_wrapper']['actions']['add_fieldset'] = [
            '#type' => 'submit',
            '#value' => 'Add new group',
            '#submit' => ['::addOneGroup'],
            '#limit_validation_errors' => [],
            '#ajax' => [
                'callback' => '::fieldsetCallback',
                'wrapper' => 'microfeedback-therapy-areas-products-tags',
            ],
        ];

        $form['microfeedback_page_wrapper']['microfeedback_description'] = [
            '#type' => 'container',
            '#markup' => "In the field below, you can enter an html class of existing container on the page where you wish to place microfeedback iframe.
                The position and styles for this container depend on site's own styles.Important - field is optional,
                if it's empty the position of the iframe is according default block position!",
            '#attributes' => [
                'class' => ['microfeedback-description'],
                'style' => 'margin-top:30px;'
            ],
        ];

        $form['microfeedback_page_wrapper']['microfeedback_html_class'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Html class of existing container'),
            '#default_value' => (!empty($microfeedback_values['microfeedback_html_class']) ? $microfeedback_values['microfeedback_html_class'] : []),
        ];

        // Probably removes some type of cache
        $form_state->setCached(FALSE);

        //actions
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save Settings')
        ];
        return $form;
    }

     /**
     * Adds one more group to the fieldset list
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function addOneGroup(array &$form, FormStateInterface $form_state)
    {
        $temporary_field_data = [
            "tags_section_fieldset" => [
              "therapy_area_tags" => [
                "therapy_area_id" => "",
              ],
              "product_tags" => [
               "product_id" => "",
              ],
              "function" => "",
              "section" => ""
            ],
            "apply_to_pages_fieldset" => [
              "apply_to_pages" => [
                'option_id' => '', 
                'option_name' => ''
              ],
              "content_types" => [
                'option_id' => '', 
                'option_name' => ''
              ]
            ]
        ];

        $temporary_fields = $form_state->get('microfeedback_temporary_fields');
        $temporary_fields[] = $temporary_field_data;
        $form_state->set('microfeedback_temporary_fields', $temporary_fields);
        $form_state->setRebuild();
    }

    /**
     * Remove a group on it's remove button click
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function removeGroup(array &$form, FormStateInterface $form_state)
    {
        $group_id = $form_state->getTriggeringElement()['#id'];
        $temporary_fields = $form_state->get('microfeedback_temporary_fields_to_remove') ?: [];

        array_push($temporary_fields, $group_id);
        $form_state->set('microfeedback_temporary_fields_to_remove', $temporary_fields);
        $form_state->setRebuild();
    }

    /**
     * The callback function to be executed with AJAX on add button click
     * @param array $form
     * @param FormStateInterface $form_state
     * 
     * @return array
     */
    public function fieldsetCallback(array &$form, FormStateInterface $form_state)
    {
        return $form['microfeedback_page_wrapper'];
    }

    /**
     * Submit form logic
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->config('takeda_alchemer_feedback.settings');
        $tags_helper = \Drupal::service('takeda_alchemer.tags_helper');

        // Takedaa alchemer values check - blank array if no data so that it can be countable
        $microfeedback_values = $config->get('microfeedback_values') ?: []; 
        $microfeedback_groups = isset($microfeedback_values['microfeedback_groups']) ? $microfeedback_values['microfeedback_groups'] : []; 
        
         // Get the values from the submitted form data
        $values = $form_state->getValue(['microfeedback_page_wrapper']);
        unset($values['actions']); 
        foreach ($microfeedback_groups as $key=>$group) {

            //retrieve selected nodes from current group
            $selected_nodes = $group['apply_to_pages_fieldset']['apply_to_pages'];
            $selected_nodes = array_values($selected_nodes);
            $temporary_fields_to_remove = $form_state->get('microfeedback_temporary_fields_to_remove');
            if (!empty($temporary_fields_to_remove) && in_array($key, $temporary_fields_to_remove)) {

                //disable microfeedback  when delete the group
                $tags_helper->disableAlchemer($selected_nodes, CONFIG::ALCHEMER_MICROFEEDBACK_TABLE);
                continue;
            }

            $current_selected_nodes = !empty($values['microfeedback_groups'][$key]) ? $values['microfeedback_groups'][$key]['apply_to_pages_fieldset']['apply_to_pages'] : [];
            $current_selected_nodes = array_values($current_selected_nodes);
            
            $nodes_to_remove_from_select = [];

            if (count(array_diff($selected_nodes, $current_selected_nodes)) > 0) {
                $nodes_to_remove_from_select = array_diff($selected_nodes, $current_selected_nodes);

                //disable microfeedback for the removed groups from select
                $tags_helper->disableAlchemer($nodes_to_remove_from_select, CONFIG::ALCHEMER_MICROFEEDBACK_TABLE);
            }
        }
        
        if (isset($values['microfeedback_groups'])) {
             //update node_field_data with the new tags
             $tags_helper->updateAlchemerTags($values, CONFIG::ALCHEMER_MICROFEEDBACK_TABLE);
        }

        $config->set('microfeedback_values', $values);
        $config->save();
        $form_state->set('microfeedback_temporary_fields', []);
        $form_state->set('microfeedback_temporary_fields_to_remove', []);
        return parent::submitForm($form, $form_state);
    }

    public function validateTag(array &$form, FormStateInterface $form_state) {
        $value = $form['#value'];
        if (!empty($value)) {
            $type = $form['#autocomplete_route_parameters']['field_name'];

            $therapy_area = Config::FIELD_ALCHEMER_THERAPY_AREA['name'];
            $website_product = Config::FIELD_ALCHEMER_WEBSITE_PRODUCT['name'];
            $section = Config::FIELD_MICROFEEDBACK_SECTION['name'];
            $function = Config::FIELD_ALCHEMER_FUNCTION['name'];

            $itemExist = \Drupal::service('takeda_alchemer.helper')->checkItemExist($value, $type);
            if (!$itemExist) {
                switch ($type) {
                    case $therapy_area:
                        $form_state->setError($form, t('"' . $value . '" is not in the therapy areas list.'));
    
                    case $website_product:
                        $form_state->setError($form, t('"' . $value . '" is not in the products list.'));
                    
                    case $function:
                        $form_state->setError($form, t('"' . $value . '" is not in the functions list.'));

                    case $section:
                        $form_state->setError($form, t('"' . $value . '" is not in the sections list.'));
                }
            }
        }
    }
    
    /**
     * This function checks for duplicate node id values. If there are duplicate node-ids ,
     * the further execution of the form is stopped and error message is thrown on the front-end.
     * @param array $form
     * @param FormStateInterface $form_state
     * 
     * @return void
     */
    public function checkNodeInGroup(array &$form, FormStateInterface $form_state) {

        $default_values = $form_state->getValue(['microfeedback_page_wrapper']);
        foreach ($default_values['microfeedback_groups'] as $group) {
            $all_selected_nodes[] = $group['apply_to_pages_fieldset']['apply_to_pages'];
        }

        //get all node values in new array
        $new_node_array = [];
        
        foreach ($all_selected_nodes as $node) {
            foreach ($node as $k) {
                $new_node_array[] = $k;
            }
        }

        // Checks for duplicate values.
        $unique_node_array = array_unique($new_node_array);
        if (count($new_node_array) !== count($unique_node_array)) {
            $form_state->setError($form, $this->t('Duplicate nodes detected'));
        }
    }
}
