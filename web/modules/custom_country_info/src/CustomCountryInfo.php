<?php

namespace Drupal\custom_country_info;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use \GuzzleHttp\Exception\RequestException;

use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Provides route responses for the Example module.
 */
class CustomCountryInfo extends ControllerBase
{
    public function loadCountryData()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $country_tax_id = $data['countryCode'];
        $no_site_info = $data['noSiteInformation'];
        $products = $data['selectProduct'];

        if ( !empty($country_tax_id) ){
            $requested_country = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($country_tax_id);

            if ( $no_site_info ){
                $info_arr = [];
                $country_no_site_blocks = $requested_country->get('field_no_site_blocks')->getValue();

                $no_site_title = $requested_country->get('field_no_site_title')->getValue()[0]['value'];

                foreach( $country_no_site_blocks as $block ){
                    $paragraph = Paragraph::load($block['target_id']);

                    $text = $paragraph->field_text->getValue()[0]['value'];
                    $link = $paragraph->field_link_url->getValue()[0]['value'];

                    $single_card = [
                        'text' => $text,
                        'link' => $link
                    ];

                    array_push( $info_arr, $single_card );
                }

                return new JsonResponse(
                    [
                        'noSiteSectionTitle' => $no_site_title,
                        'results' => $info_arr,
                        Response::HTTP_OK
                    ]
                );

            }elseif( $products ){
                $country_email = $requested_country->get('field_contact_email')->getValue()[0]['value'];

                $products_paragraph = $requested_country->get('field_products_name_value')->getValue();
                $products_array = [];

                foreach( $products_paragraph as $product ){
                    $paragraph = Paragraph::load($product['target_id']);

                    $name = !empty($paragraph->field_product_label->getValue()) ? $paragraph->field_product_label->getValue()[0]['value'] : '';
                    $value = !empty($paragraph->field_product_value->getValue()) ? $paragraph->field_product_value->getValue()[0]['value'] : '';

                    $single_product = [
                        'name' => $name,
                        'value' => $value
                    ];

                    array_push( $products_array, $single_product );
                }

                $info_arr = [
                    'products' => $products_array,
                    'email' => $country_email
                ];

                return new JsonResponse(
                    [
                        'results' => $info_arr,
                        Response::HTTP_OK
                    ]
                );

            }else{
                $country_email = $requested_country->get('field_contact_email')->getValue()[0]['value'];
                $country_phone = $requested_country->get('field_contact_number')->getValue()[0]['value'];
        
                $info_arr = [
                    'email' => $country_email,
                    'phone' => $country_phone
                ];

                return new JsonResponse(
                    [
                        'results' => $info_arr,
                        Response::HTTP_OK
                    ]
                );
            }
        }

        return new JsonResponse(
            [
                'message' => 'No code received',
                Response::HTTP_NOT_FOUND
            ]
        );
    }
}
