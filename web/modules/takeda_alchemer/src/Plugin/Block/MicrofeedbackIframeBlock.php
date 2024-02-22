<?php
namespace Drupal\takeda_alchemer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\NodeInterface;
use Drupal\takeda_alchemer\AlchemerFieldsConfig as Config;

/**
 * Provides a Microfeedback Iframe Block.
 *
 * @Block(
 *   id = "microfeedback_iframe_block",
 *   admin_label = @Translation("Microfeedback Iframe Block"),
 *   category = @Translation("Microfeedback Iframe Block"),
 * )
 */
class MicrofeedbackIframeBlock extends BlockBase
{

    const MICROFEEDBACK_ENABLED = 1;
    const MICROFEEDBACK_DISABLED = 0;

    public function getCacheMaxAge() {
        return 0;  
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {

        $node = \Drupal::routeMatch()->getParameter('node');
        $is_admin_context = \Drupal::service('router.admin_context')->isAdminRoute();

        //default data
        $microfeedback_state = self::MICROFEEDBACK_DISABLED;
        $iframe_src_to_send = '';
        $microfeedback_iframe_class = '';

        //prevent showing this script on other type of pages including node edit pages
        if ($node instanceof NodeInterface && !$is_admin_context)
        {
            //define alchemer basic helper
            $alchemer_basic_helper = \Drupal::service('takeda_alchemer.helper');

            //define microfeedback type
            $microfeedback_survey_type = CONFIG::MICROFEEDBACK_SURVEY_TYPE;

            //extract data from microfeedback table
            $microfeedback_data = $alchemer_basic_helper->retrieveSurveyData($node, $microfeedback_survey_type);

            // if microfeedback is enabled
            if (isset($microfeedback_data['is_microfeedback_active']) && $microfeedback_data['is_microfeedback_active'])
            {
                $microfeedback_state = self::MICROFEEDBACK_ENABLED;

                //Define field config names from AlchemerFieldsConfig class
                $country = Config::FIELD_ALCHEMER_COUNTRY_NAME['name'];
                $website_name = Config::FIELD_ALCHEMER_WEBSITE_NAME['name'];
                $therapy_area = Config::FIELD_ALCHEMER_THERAPY_AREA['name'];
                $website_product = Config::FIELD_ALCHEMER_WEBSITE_PRODUCT['name'];
                $hcp_id = Config::FIELD_ALCHEMER_HCP_ID['name'];
                $customer_id = Config::FIELD_ALCHEMER_CUSTOMER_ID['name'];
                $digital_id = Config::FIELD_ALCHEMER_DIGITAL_ID['name'];
                $function = CONFIG::FIELD_ALCHEMER_FUNCTION['name'];
                $section = CONFIG::FIELD_MICROFEEDBACK_SECTION['name'];
                $url = CONFIG::FIELD_MICROFEEDBACK_URL['name'];

                //fetch takeda id
                $logged_in = \Drupal::currentUser()->isAuthenticated();
                $takeda_id = "";
                $user_beacons_to_render = [];

                // extract baic alchemer conf. data
                $alchemer_basic_config = \Drupal::config('takeda_alchemer.settings');
                $alchemer_config_data = $alchemer_basic_config->get('takeda_alchemer_values');
                $country_name = $alchemer_config_data['takeda_alchemer_country'];
                $microfeedback_survey_url = $alchemer_config_data['takeda_alchemer_microfeedback_survey_url'];

                //get site name
                $site_name = \Drupal::config('system.site')->get('name');

                // extract microfeedback conf data
                $microfeedback_config = \Drupal::config('takeda_alchemer_feedback.settings');
                $microfeedback_config_data = $microfeedback_config->get('microfeedback_values');
                $microfeedback_iframe_class =  $microfeedback_config_data['microfeedback_html_class'];

                if ($logged_in)
                {
                    $user_id = \Drupal::currentUser()->id();
                    $takeda_id = $alchemer_basic_helper->retrieveTakedaId($user_id);
                    $user_beacons_to_render = isset($takeda_id) ? $alchemer_basic_helper->getUserDigitalId($takeda_id) : [];
                }
                //define params for url
                $params = [];

                $microfeedback_tags = json_decode($microfeedback_data['microfeedback_tags'], true);
                if (!empty($microfeedback_tags))
                {

                    $active_microfeedback_tags = array_keys($microfeedback_tags, 1, true);

                    foreach ($active_microfeedback_tags as $tag)
                    {
                        switch ($tag)
                        {
                            case $website_name:
                                $params['webname'] = $site_name;
                            break;

                            case $country:
                                if (!empty($country_name))
                                {
                                  //get only country code
                                  $country_code = $alchemer_basic_helper->getCode(trim($country_name), $country);
                                  if (!empty($country_code))
                                  {
                                    $params['co'] = $country_code;
                                  }
                                }
                            break;

                            case $therapy_area:
                                $therapy_areas = array_values(json_decode($microfeedback_data['microfeedback_therapy_areas'], true));
                                foreach ($therapy_areas as $key => $value)
                                {
                                    if (!empty($value))
                                    {
                                        if ($key == 0)
                                        {
                                            $key = "";
                                        }
                                        else
                                        {
                                            $key += 1;
                                        }
                                        //send only therapy area code
                                        $code = $alchemer_basic_helper->getCode($value, $therapy_area);
                                        if (!empty($code))
                                        {
                                            $params['ta' . $key] = $code;
                                        }
                                    }
                                }
                            break;

                            case $website_product:
                                $products = array_values(json_decode($microfeedback_data['microfeedback_products'], true));
                                foreach ($products as $key => $value)
                                {
                                    if (!empty($value))
                                    {
                                        if ($key == 0)
                                        {
                                            $key = "";
                                        }
                                        else
                                        {
                                            $key += 1;
                                        }
                                        //send only product code
                                        $code = $alchemer_basic_helper->getCode($value, $website_product);
                                        if (!empty($code))
                                        {
                                            $params['pr' . $key] = $code;
                                        }
                                    }
                                }
                            break;

                            case $section:
                                $section_value = $microfeedback_data['microfeedback_section'];
                                if (!empty($section_value))
                                {
                                    $params['sc'] = $section_value;
                                }
                            break;

                            case $function:
                                $function_value = $microfeedback_data['microfeedback_function'];
                                if (!empty($function_value))
                                {

                                    $function_code = '';
                                    if ($function_value === "Commercial")
                                    {
                                        $function_code = 'cm';
                                    }

                                    if ($function_value === "Medical")
                                    {
                                        $function_code = 'md';
                                    }
                                    $params['fn'] = $function_code;
                                }
                            break;

                            case $url:
                                $node_url = $node->toUrl('canonical', ['absolute' => true])
                                    ->toString();
                                $params['url'] = $node_url;

                            case $hcp_id:
                                // if HCP ID is checked as active tag, add also customerID and digitalID
                                if (isset($user_beacons_to_render[$customer_id]))
                                {
                                    $params[$customer_id] = $user_beacons_to_render[$customer_id];
                                }

                                if (isset($user_beacons_to_render[$digital_id]))
                                {
                                    $params[$digital_id] = $user_beacons_to_render[$digital_id];
                                }
                            break;
                        }
                    }

                    //build url
                    $url_data = urldecode(http_build_query($params, '', '&'));
                    $iframe_src_to_send = $microfeedback_survey_url . '?' . $url_data;

                    //create data array for twig template
                    $data = [];
                    $data['src'] = $iframe_src_to_send;
                    $data['microfeedback_active'] = $microfeedback_state;
                    $data['iframe_container_class'] = $microfeedback_iframe_class;

                    return [
                      '#theme' => 'microfeedback_iframe',
                      '#attached' => [
                        'library' => [
                          'takeda_alchemer/takeda-alchemer',
                        ],
                      ],
                      '#data' => $data,
                    ];
                }
            }
        }
    }
}

