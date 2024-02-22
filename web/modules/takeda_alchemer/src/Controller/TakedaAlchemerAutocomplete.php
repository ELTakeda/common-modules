<?php

namespace Drupal\takeda_alchemer\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Xss;
use Drupal\takeda_alchemer\AlchemerFieldsConfig as Config;

/**
 * Class TakedaAlchemerAutocomplete
 * @package Drupal\takeda_alchemer\Controller
 */
class TakedaAlchemerAutocomplete extends Config
{
  const ENTITY_LABEL = 0;
  const ENTVALUE = 1;


  /**
   * @return JsonResponse
   */
  public function  handleAutocomplete(Request $request)
  {
    $data = [];
    $input = $request->query->get('q');
    $field = $request->query->get('field_name');
    if (!$input) {
      return new JsonResponse($data);
    }
    $input = Xss::filter($input);
    
    $config = \Drupal::config('takeda_alchemer.settings');
    $values = $config->get('takeda_alchemer_values');

    switch($field) {
        case self::FIELD_ALCHEMER_THERAPY_AREA['name']:
            $therapy_areas = $values['takeda_alchemer_therapy_areas'];
            $data = $this->createInfo($therapy_areas, $input, $field);
            break;

        case self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name']:
            $products =  $values['takeda_alchemer_products'];
            $data = $this->createInfo($products, $input, $field);
            break;

        case self::FIELD_ALCHEMER_COUNTRY_NAME['name']:
            $countries = $values['takeda_alchemer_country_codes'];
            $data = $this->createInfo($countries, $input, $field);
            break;

        case self::FIELD_ALCHEMER_FUNCTION['name']:
            $functions = $values['takeda_alchemer_function_labels'];
            $data = $this->createInfo($functions, $input, $field);
            break;
        
        case self::FIELD_MICROFEEDBACK_SECTION['name']:
            $sections = $values['takeda_alchemer_section_labels'];
            $data = $this->createInfo($sections, $input, $field);
            break;
    }

    return new JsonResponse($data);
  }
  
  /**
   * createInfo
   *
   * @param  string $items
   * @param  string $input
   * @param  string $field
   * @return array
   */
  public function createInfo($items, $input, $field) {
    $input = strtolower($input);
    $results = [];

      if (!empty($items)) {
        $items = array_filter(explode("\n", $items));
        //Convert each item(label;code) to array - [0] => label, [1] => code
        $converted_items= array_map(fn($val) => explode(";", $val), $items);

        foreach ($converted_items as $entity) {
        //add item in suggestion list when input value contains in item.
          if (preg_match("/{$input}/i", strtolower($entity[0]))) {
            switch ($field) {

              case self::FIELD_ALCHEMER_COUNTRY_NAME['name']:
              case self::FIELD_ALCHEMER_FUNCTION['name']:
              case self::FIELD_MICROFEEDBACK_SECTION['name']:
                  $results[] = [
                    'label' => $entity[0],
                    'value' => $entity[0]
                  ];
                  break;
                  
              default:
                  $results[] = [
                    'label' => $entity[0],
                    'value' => $entity[0].' - '.$entity[1]
                  ];
                  break;
            }
          }
        }
      }
    return $results;
  }
}