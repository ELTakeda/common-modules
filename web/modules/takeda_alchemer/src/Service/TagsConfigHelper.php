<?php
namespace Drupal\takeda_alchemer\Service;

use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormStateInterface;
use Drupal\takeda_alchemer\AlchemerFieldsConfig as Config;

/**
 * Class TagsConfigHelper
 * This class stores helper functions for the new alchemer tag functionality.
 *
 */
class TagsConfigHelper extends Config
{

    const ALCHEMER_ENABLED = 1;
    const ALCHEMER_DISABLED = 0;

    /**
     * Update group id on edit action.
     * Returns the same group id if when saving the page we haven't made any changes to the tags,
     * and null if there are any changes.
     *
     * @param FormStateInterface $form_state
     *
     * @return null|string
     */
    public function updateGroupId(FormStateInterface $form_state, $type) {

        //define result
        $result = null;

        //menus
        $alchemer_tags_menu = $form_state->getValue('alchemer-'.$type.'-menu')['alchemer-'.$type.'-tags'];
        $therapy_area_values = $alchemer_tags_menu['therapy-area-menu'] ? : [];
        $product_menu_values = $alchemer_tags_menu['website-product-menu'] ? : [];

        //tags
        $alchemer_state = $form_state->getValue('alchemer-'.$type.'-menu')['alchemer-'.$type.'-enabled'];
        $therapy_area_tag = $alchemer_tags_menu[self::FIELD_ALCHEMER_THERAPY_AREA['name']];
        $website_product_tag = $alchemer_tags_menu[self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name']];
        $function_tag = $alchemer_tags_menu[self::FIELD_ALCHEMER_FUNCTION['name']];
        $section_tag = isset($alchemer_tags_menu[self::FIELD_MICROFEEDBACK_SECTION['name']]) ? ($alchemer_tags_menu[self::FIELD_MICROFEEDBACK_SECTION['name']]) : '';

        //function
        $function_value = $alchemer_tags_menu['function-menu'][0];

        //section
        $section_value = isset($alchemer_tags_menu['section-menu']) ? $alchemer_tags_menu['section-menu'][0] : '';

        //retrieve old tags saved
        $old_tags = $form_state->getStorage()[''.$type.'_old_node_version'];

        if ($old_tags) {
            $old_therapy_areas = !empty($old_tags[''.$type.'_therapy_areas']) ? json_decode($old_tags[''.$type.'_therapy_areas'], true) : [];
            $old_products =  !empty($old_tags[''.$type.'_products']) ? json_decode($old_tags[''.$type.'_products'], true) : [];
            $group_id = $old_tags[''.$type.'_group_id'];

            //all new tags array
            $new_tags_merged = array_merge($therapy_area_values, $product_menu_values);
            $old_tags_merged = array_merge($old_therapy_areas, $old_products);

            //function
            $old_function_value = $old_tags[''.$type.'_function'];

            //section
            $old_section_value = isset($old_tags['microfeedback_section']) ? $old_tags['microfeedback_section'] : '';

            //find the full differences between old and new tags
            $differences = array_merge(array_diff($new_tags_merged, $old_tags_merged), array_diff($old_tags_merged, $new_tags_merged));
            if (count($differences) > 0 || $alchemer_state === self::ALCHEMER_DISABLED ||
             $therapy_area_tag === self::ALCHEMER_DISABLED || $website_product_tag === self::ALCHEMER_DISABLED) {
                $result = null;
            }


            elseif ($function_value !== $old_function_value || $function_tag === self::ALCHEMER_DISABLED ) {
                $result = null;
            }

            //if type is microfeedback, check section
            elseif (isset($old_tags['microfeedback_section']) &&
             ($section_value !== $old_section_value || $section_tag === self::ALCHEMER_DISABLED)) {
                $result = null;
            }

            else {
                $result = $group_id;
            }
        }

        return $result ;
    }

    /**
     * Turns off Alchimer and sets the group id to NULL
     * @param array $param
     * @param string $table
     * @return void
     *
     */
    public function disableAlchemer($nodes, $table) {
        if (!empty($nodes)) {

            $fields = [];
            if ($table === self::ALCHEMER_POPUP_TABLE) {
                //create array with fields and their values to insert
                $fields['is_popup_active'] = self::ALCHEMER_DISABLED;
                $fields['popup_tags'] = json_encode(self::popupDefaultTagValues());
                $fields['popup_group_id'] = null;
            }

            elseif ($table === self::ALCHEMER_MICROFEEDBACK_TABLE) {
                $fields['is_microfeedback_active'] = self::ALCHEMER_DISABLED;
                $fields['microfeedback_tags'] = json_encode(self::microfeedbackDefaultTagValues());
                $fields['microfeedback_group_id'] = null;
            }

            //initiliaze db
            $database = \Drupal::database();
            $database->update($table)
            ->fields($fields)
            ->condition('nid', $nodes, 'IN')
            ->execute();
        }
    }

    /**
     * Update takeda_alchemer_popup and takeda_alchemer_microfeedback tables
     * @param array $values
     * @param string $table
     * @return void
     */
    public function updateAlchemerTags($values, $table) {
        $type = self::getSurveyTypeByTable($table);
        if ($type === self::POPUP_SURVEY_TYPE) {
            $groups = isset($values['alchemer_popup_groups']) ? array_values($values['alchemer_popup_groups']) : [];
        }

        else {
            $groups = isset($values['microfeedback_groups']) ? array_values($values['microfeedback_groups']) : [];
        }

        if ($groups) {

            foreach ($groups as $key=>$group) {

                //retrieve selected nodes from current group
                $selected_nodes = $group['apply_to_pages_fieldset']['apply_to_pages'];

                //if no selected nodes to not execute the query
                if ($selected_nodes) {
                    $selected_nodes = array_values($selected_nodes);

                    //set group id to current key
                    $group_id = $key;

                    //retrive therapy area tags from current group
                    $areas = $group['tags_section_fieldset']['therapy_area_tags'];
                    $areas = json_encode($areas);

                    //retrieve product tags from current group
                    $products = $group['tags_section_fieldset']['product_tags'];
                    $products = json_encode($products);

                    //retrieve function from current group
                    $function = $group['tags_section_fieldset']['function'];

                    //set replacements
                    $replacements = [
                        self::FIELD_ALCHEMER_THERAPY_AREA['name'] => 1,
                        self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name'] => 1,
                        self::FIELD_ALCHEMER_FUNCTION['name'] => 1,
                    ];

                    $connection = \Drupal::database();
                    //define fields
                    $fields = [];

                    if ($table === self::ALCHEMER_POPUP_TABLE) {
                        //create array with fields and their values to insert
                        $fields['is_popup_active'] = self::ALCHEMER_ENABLED;
                        $fields['popup_therapy_areas'] = $areas;
                        $fields['popup_products'] = $products;
                        $fields['popup_function'] = $function;
                        $fields['popup_group_id'] = $group_id;

                        // get default popup tags
                        $default_alchemer_tags = self::popupDefaultTagValues();

                    }

                    else {
                        //retrieve section from the current group
                        $section = $group['tags_section_fieldset']['section'];

                        $replacements[self::FIELD_MICROFEEDBACK_SECTION['name']] = 1;

                        //create array with fields and their values to insert
                        $fields['is_microfeedback_active'] = self::ALCHEMER_ENABLED;
                        $fields['microfeedback_therapy_areas'] = $areas;
                        $fields['microfeedback_products'] = $products;
                        $fields['microfeedback_function'] = $function;
                        $fields['microfeedback_section'] = $section;
                        $fields['microfeedback_group_id'] = $group_id;

                        // get default microfeedback tags
                        $default_alchemer_tags = self::microfeedbackDefaultTagValues();

                    }

                    $default_alchemer_tags = array_replace($default_alchemer_tags, $replacements);
                    $fields[''.$type.'_tags'] = json_encode($default_alchemer_tags);
                    $data = [];

                    foreach ($selected_nodes as $key => $val) {
                        $fields['nid'] = $val;
                        $data[$key] = $fields;
                    }
                    //update existing and insert new records
                    $upsert = $connection->upsert($table)
                    ->fields(array_keys($fields))
                    ->key('nid');

                    foreach ($data as $record) {
                        $upsert->values($record);
                    }

                    $upsert->execute();
                }
            }
        }
    }

    /**
     * @param mixed $group_id
     * @param string $table
     * @return array
     */
    public function getAlchemerTagsByGroupId($group_id, $table){
        $database = \Drupal::database();

        if ($table === self::ALCHEMER_POPUP_TABLE) {
            $result = $database->select('takeda_alchemer_popup', 'f')
            ->fields('f', ['popup_therapy_areas', 'popup_products', 'popup_function', 'nid'])
            ->condition('popup_group_id', $group_id, '=')
            ->condition('is_popup_active', self::ALCHEMER_ENABLED)
            ->execute();
        }

        elseif ($table === self::ALCHEMER_MICROFEEDBACK_TABLE) {
            $result = $database->select('takeda_alchemer_microfeedback', 'f')
            ->fields('f', ['microfeedback_therapy_areas', 'microfeedback_products', 'microfeedback_function', 'microfeedback_section', 'nid'])
            ->condition('microfeedback_group_id', $group_id, '=')
            ->condition('is_microfeedback_active', self::ALCHEMER_ENABLED)
            ->execute();
        }

        $defaults = [];
        while ($record = $result->fetchAssoc()) {
            $defaults[] = $record;
        }

        return $defaults;
    }

    /**
     * Retrieve all content types names
     * @return array
     */
    public function retrieveAllContentTypes() {
        $entityTypeManager = \Drupal::service('entity_type.manager');

        $types = [];
        $types['all'] = "All";
        $contentTypes = $entityTypeManager->getStorage('node_type')->loadMultiple();
        foreach ($contentTypes as $contentType) {
            $types[$contentType->id()] = $contentType->label();
        }
        return $types;
    }

    /**
     * Retrieve all node titles
     * @return array
     */
    public function retrieveAllNodesForSelect($content_types) {
        $options = [];
        if ($content_types) {
            $values = array_keys($content_types);
            // if "all" option exists in content types array retrieve all node ids.
            if (in_array("all", $values)) {
                $articleIds = \Drupal::entityQuery('node')
                  ->accessCheck(FALSE)
                  ->execute();
            }

            else {
                $articleIds = \Drupal::entityQuery('node')
                ->condition('type', $values, 'in')
                ->accessCheck(FALSE)
                ->execute();
            }

            if ($articleIds) {
                $allArticles = Node::loadMultiple($articleIds);
                foreach ($allArticles as $key=>$value) {
                    $options[$key] = $value->getTitle();
                }
                //sort article titles by ascending alphabetical order
                asort($options);
            }
        }
        return $options;
    }
}
