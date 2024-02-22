<?php

namespace Drupal\takeda_consents\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides route responses for the Example module.
 */
class TakedaConsentsController extends ControllerBase
{
    public function sendUserConsents() {
        // Get the posted form data
        $email = $_POST['email'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $customerId = $_POST['takeda_id'];
        $country_code = $_POST['country_code'];
        $language = $_POST['language'];
        $purposes = $_POST['takeda_consents_values'];
        $page_language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        
        // Get the saved data of the config
        $config = \Drupal::config('takeda_consents.settings');
        $takeda_consents_values = $config->get('takeda_consents_values') ?: []; // Takeda consents values check - blank array if no data so that it can be countable
        $takeda_consents_values_purposes = $takeda_consents_values['takeda_consents_purposes'];
        $takeda_consents_language_values = $config->get('takeda_consents_language_values') ?: []; // Takeda consents language values check - blank array if no data so that it can be countable

        // Get the general consent info
        $omnichannel_url = $takeda_consents_values['omnichannel_url'];
        $omnichannel_api_key = $takeda_consents_values['omnichannel_api_key'];
        $omnichannel_api_secret = $takeda_consents_values['omnichannel_api_secret'];

        foreach ($purposes as $purpose_key => $purpose_value) {
            // Check if purposes is checked
            if (is_array($purpose_value)) {
                if (!$purpose_value['purpose_checkbox']) { // If it isn't checked, skip to the next purpose
                    continue;
                }

                unset($purpose_value['purpose_checkbox']); // Remove it for easier iterations of the preferences
            } else { // Consents without options
                if (!$purpose_value) { // If it isn't checked, skip to the next purpose
                    continue;
                }
            }

            // Get the purpose index
            $current_purpose_index = str_replace('tc_', '', $purpose_key);

            $current_purpose_data = $takeda_consents_values_purposes[$current_purpose_index];
            $purpose_id = $current_purpose_data['purpose_id'];
            $purpose_has_preferences = $current_purpose_data['purpose_preference_boolean'];
            $purpose_request_info = $current_purpose_data['request_info'];
            $purpose_identifier_type = $current_purpose_data['purpose_identifier']; // email or takedaid
            $purpose_identifier = $email; // supporting email or takedaid currently

            if ($purpose_identifier_type === 'takedaid') {
                $purpose_identifier = $customerId; // The TakedaID
            }

            $purposes = [];

            // Check if the purpose is checked
            if ($purpose_has_preferences) {
                $preferences = [];

                // Go over each defined preference
                foreach ($current_purpose_data['preferences'] as $preference_key => $preference_value) {
                    $current_preference_data = $preference_value['preference_fieldset'];
                    $current_preference_id = $current_preference_data['preference_id'];
                    $current_preference_options = $current_preference_data['preference_options'];
                    $current_preference_hidden_options = $current_preference_data['preference_hidden_boolean']; // 0 or 1
                    $current_language_options = $takeda_consents_language_values[$current_purpose_index][$page_language][$preference_key];
                    $current_language_option_display = $takeda_consents_language_values[$current_purpose_index][$page_language][$preference_key]['preference_display']['display_setting'];
                    $current_checked_options = isset($purpose_value[$preference_key]) ? $purpose_value[$preference_key] : [];

                    $options = [];

                    foreach ($current_preference_options as $option_key => $option_value) {
                        $current_option_data = $option_value['option_fieldset'];
                        $current_option_id = $current_option_data['option_id'];

                        // Check if the option is enabled for the lang-country
                        $isEnabled = isset($current_language_options[$current_option_id]) ? $current_language_options[$current_option_id]['value'] : 0; // 0 or 1

                        // Check if the option is checked
                        // Current language option display has a value of 0 or 1
                        // Current checked options have a value of 0 or 1
                        // If the display options is on, get the options from the form
                        // If the display options are off, check all options
                        $isChecked = $current_language_option_display ? (isset($current_checked_options[$current_option_id]) ? $current_checked_options[$current_option_id] : 0) : 1; // 0 or 1

                        // In case of hidden inputs, they have to be enabled and are always selected if enabled
                        // Otherwise keep the above checked value
                        $isChecked = $current_preference_hidden_options ? 1 : $isChecked;

                        // Then add it to the options array
                        if ($isEnabled && $isChecked) {
                            array_push($options, $current_option_id);
                        }
                    }

                    $current_preference = [
                        "Id" => $current_preference_id,
                        "Options" => $options
                    ];

                    if (count($options) > 0) {
                        array_push($preferences, $current_preference);
                    }
                }
                
                $purposes = [
                    [
                        "Id" => $purpose_id,
                        "TransactionType" => "CONFIRMED",
                        "CustomPreferences" => $preferences
                    ]
                ];
            } else { // Consents without preferences and options
                $purposes = [
                    [
                        "Id" => $purpose_id,
                        "TransactionType" => "CONFIRMED",
                        "CustomPreferences" => []
                    ]
                ];
            }

            $site_url = \Drupal::request()->getSchemeAndHttpHost();
            $current_date = date('Y-m-d\TH:i:s');

            $data = [
                "identifier" => $purpose_identifier,
                "language" => $language,
                "dataElements" => [
                    "firstName" => $first_name,
                    "lastName" => $last_name,
                    "sourceSite" => $site_url, // Possibly not required
                    "countryCode" => $country_code,
                    "takedaId" => $customerId // Received from the TakedaId module hook
                ],
                "consentDate" => $current_date,
                "requestInfo"  => $purpose_request_info,
                "purposes" => $purposes,
                "additionalInfo" => [
                    "callBackUrl" => "" // Used to pass back the consent status
                ]
            ];

            $data_encoded = json_encode($data);

            // Log the attempt
            \Drupal::logger('takeda_consents')->notice('Attempting to send the following data:<pre><code>' . print_r($data, true) . '</code></pre>');

            try {
                // Get the Drupal http client
                $client = \Drupal::httpClient();
    
                // Send the request
                $request_consent_registration = $client->post($omnichannel_url, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'client_id' => $omnichannel_api_key,
                        'client_secret' => $omnichannel_api_secret,
                    ],
                    'body' => $data_encoded
                ]);
    
                $response = $request_consent_registration;
                $response_status_code = $response->getStatusCode();
    
                // Set a message to the system logs
                // Set a message to the user
                if ($response_status_code == 200 || $response_status_code == 201) {
                    \Drupal::logger('takeda_consents')->notice('Takeda Customer ID ' . $customerId . ' has sent a consent for a purpose with ID ' . $purpose_id);
                    \Drupal::messenger()->addStatus('Your consent data was received successfully.');
                } else {
                    \Drupal::logger('takeda_consents')->error('Takeda Customer ID ' . $customerId . ' has sent a consent for a purpose with ID ' . $purpose_id . ' but received an error.');
                    \Drupal::messenger()->addStatus($this->t('An error occurred when receiving your consent data.'));
                }
            } catch (\Exception $e) {
                \Drupal::logger('takeda_consents')->error('Takeda Customer ID ' . $customerId . ' has sent a consent for a purpose with ID ' . $purpose_id . ' but received an error.');
                \Drupal::messenger()->addStatus($this->t('An error occurred when receiving your consent data.'));
            }
        }

        // Redirect to login page
        $site_url = \Drupal::request()->getSchemeAndHttpHost();
        $current_language_code = \Drupal::languageManager()->getCurrentLanguage()->getId(); // The current langauge code - example en-gb
        $path = $site_url . '/' . $current_language_code . '/login';
        return new RedirectResponse($path);
    }
}
