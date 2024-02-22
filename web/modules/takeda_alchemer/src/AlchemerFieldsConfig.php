<?php

namespace Drupal\takeda_alchemer;

/**
 * Class AlchemerFieldsConfig
 * Defines the names of form fields, tables, and other elements..
 */
class AlchemerFieldsConfig
{
    //the fields that are the same for both forms(popup and microfeedback)
    const FIELD_ALCHEMER_COUNTRY_NAME = ['name' => 'country-name', 'label' => 'Country'];
    const FIELD_ALCHEMER_WEBSITE_NAME = ['name' => 'website-name', 'label' => 'Website Name'];
    const FIELD_ALCHEMER_THERAPY_AREA = ['name' => 'therapy-area', 'label' => 'Therapy Area'];
    const FIELD_ALCHEMER_WEBSITE_PRODUCT = ['name' => 'website-product', 'label' => 'Product of the website'];
    const FIELD_ALCHEMER_HCP_ID = ['name' => 'hcp-id', 'label' => 'HCP ID'];
    const FIELD_ALCHEMER_CUSTOMER_ID = ['name' => 'hgid', 'label' => 'Customer ID'];
    const FIELD_ALCHEMER_DIGITAL_ID = ['name' => 'heid', 'label' => 'Digital ID'];
    const FIELD_ALCHEMER_FUNCTION = ['name' => 'function', 'label' => 'Function'];

    //takeda id configuration
    const TAKEDA_ID_ENVIRONMENT_URL_DEV = 'https://api-us-np.takeda.com/dev/security-takedaid-api/v1/users';
    const TAKEDA_ID_ENVIRONMENT_URL_SIT = 'https://api-us-np.takeda.com/sit/security-takedaid-api/v1/users';
    const TAKEDA_ID_ENVIRONMENT_URL_UAT = 'https://api-us-np.takeda.com/uat/security-takedaid-api/v1/users';
    const TAKEDA_ID_ENVIRONMENT_URL_PROD = 'https://api-us.takeda.com/security-takedaid-api/v1/users';

    //for popup survey
    const THERAPY_AREA_NUM = 5;
    const WEBSITE_PRODUCT_NUM = 5;

    //for microfeedback survey
    const FIELD_MICROFEEDBACK_SECTION = ['name' => 'microfeedback-section', 'label' => 'Section'];
    const FIELD_MICROFEEDBACK_URL = ['name' => 'microfeedback-url', 'label' => 'URL'];
    const M_THERAPY_AREA_NUM = 3;
    const M_WEBSITE_PRODUCT_NUM = 3;

    //table names
    const ALCHEMER_POPUP_TABLE = 'takeda_alchemer_popup';
    const ALCHEMER_MICROFEEDBACK_TABLE = 'takeda_alchemer_microfeedback';

    //survey types
    const POPUP_SURVEY_TYPE = 'popup';
    const MICROFEEDBACK_SURVEY_TYPE = 'microfeedback';

    /**
     * @return array
     */
    public static function popupDefaultTagValues() {

        return [
            self::FIELD_ALCHEMER_COUNTRY_NAME['name'] => 1,
            self::FIELD_ALCHEMER_WEBSITE_NAME['name'] => 1,
            self::FIELD_ALCHEMER_THERAPY_AREA['name'] => 1,
            self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name'] => 0,
            self::FIELD_ALCHEMER_HCP_ID['name'] => 0,
            self::FIELD_ALCHEMER_FUNCTION['name'] => 1,  
        ];
    }

    /**
     * @return array
     */
    public static function microfeedbackDefaultTagValues() {

        return [
            self::FIELD_ALCHEMER_COUNTRY_NAME['name'] => 1,
            self::FIELD_ALCHEMER_WEBSITE_NAME['name'] => 1,
            self::FIELD_ALCHEMER_THERAPY_AREA['name'] => 1,
            self::FIELD_ALCHEMER_WEBSITE_PRODUCT['name'] => 0,
            self::FIELD_ALCHEMER_HCP_ID['name'] => 0,
            self::FIELD_ALCHEMER_FUNCTION['name'] => 1,
            self::FIELD_MICROFEEDBACK_SECTION['name'] => 0,
            self::FIELD_MICROFEEDBACK_URL['name'] => 1,
        ];
    }

    /**
     * @param string $type
     * 
     * @return string
     */
    public static function getSurveyTableByType($type) {

        $table = '';
        if ($type === self::POPUP_SURVEY_TYPE) {
            $table = self::ALCHEMER_POPUP_TABLE;
        }

        elseif ($type === self::MICROFEEDBACK_SURVEY_TYPE) {
            $table = self::ALCHEMER_MICROFEEDBACK_TABLE;
        }
        return $table;
    }
    
     /**
     * @param string $type
     * 
     * @return string
     */
    public static function getSurveyTypeByTable($table) {

        $type = '';
        if ($table === self::ALCHEMER_POPUP_TABLE) {
            $type = self::POPUP_SURVEY_TYPE;
        }

        elseif ($table === self::ALCHEMER_MICROFEEDBACK_TABLE) {
            $type = self::MICROFEEDBACK_SURVEY_TYPE;
        }

        return $type;
    }
}
