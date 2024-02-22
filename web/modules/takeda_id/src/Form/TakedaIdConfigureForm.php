<?php

namespace Drupal\takeda_id\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Entity;
use Drupal\Core\Url;
use Drupal\takeda_id\TakedaIdInterface;

/**
 * Plugin configuration form.
 */
class TakedaIdConfigureForm extends ConfigFormBase
{

    /**
     * Define constants used by this class
     */
    const DRUPAL_FIELD_ID = '#id';
    const DRUPAL_FIELD_TYPE = '#type';
    const DRUPAL_FIELD_TITLE = '#title';
    const DRUPAL_FIELD_DESCRIPTION = '#description';
    const DRUPAL_FIELD_DEFAULT = '#default_value';
    const DRUPAL_FIELD_DISABLED = '#disabled';
    const DRUPAL_FIELD_SIZE = '#size';
    const DRUPAL_FIELD_MAXLENGTH = '#maxlength';
    const DRUPAL_FIELD_OPTIONS = '#options';
    const DRUPAL_FIELD_VALUE = '#value';
    const DRUPAL_FIELD_ATTRIBUTES = '#attributes';
    const DRUPAL_FIELD_STATES = '#states';

    /**
     * Determines the ID of a form.
     */
    public function getFormId()
    {
        return 'takeda_id_configure';
    }

    /**
     * Gets the configuration names that will be editable.
     */
    public function getEditableConfigNames()
    {
        return [
            'takeda_id.settings'
        ];
    }

    /**
     * Form constructor.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attached'] = [
            'library' => [
                'takeda_id/configuration',
                'takeda_id/configure_form'
            ],
        ];
        // Read Settings.
        $config = $this->config(TakedaIdInterface::CONFIG_OBJECT_NAME);
 
        // General settings.
        $form['takeda_id_configure_api'] = [
            self::DRUPAL_FIELD_TYPE => 'fieldset',
            self::DRUPAL_FIELD_TITLE => $this->t('Takeda ID & Omnichannel API Settings')
        ];

        $form['takeda_id_configure_api']['api_url'] = [
            self::DRUPAL_FIELD_ID => 'api_url',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Takeda ID API URL'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('Takeda ID (Mulesoft) API URL'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('api_url')) ? $config->get('api_url') : 'https://api-us-np.takeda.com/dev/security-takedaid-api/v1'),
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];

        $form['takeda_id_configure_api']['lead_api_url'] = [
            self::DRUPAL_FIELD_ID => 'lead_api_url',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Lead API URL'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('Omnichannel (Mulesoft) Lead / HCP Conversion API URL'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('lead_api_url')) ? $config->get('lead_api_url') : 'https://api-us-aws-dev.takeda.com/dev/gcc-omnichannel-api/v1'),
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];


        $form['takeda_id_configure_api']['invitations_api_url'] = [
            self::DRUPAL_FIELD_ID => 'invitations_api_url',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Invitations API URL'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('Invitations API URL. No trailing slash. Used to lookup Takeda ID invitation Tokens'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('invitations_api_url')) ? $config->get('invitations_api_url') : 'https://api-us-aws-dev2.takeda.com/dev/gcc-sendInvitations-api/v1'),
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];

        $form['takeda_id_configure_api']['multichannel_api_url'] = [
            self::DRUPAL_FIELD_ID => 'multichannel_api_url',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Multichannel API URL'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('Multichannel API URL.'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('multichannel_api_url')) ? $config->get('multichannel_api_url') : 'https://api-us-aws-dev2.takeda.com/dev/gcc-multichannelactivity-api/v1'),
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];

        $form['takeda_id_configure_api']['api_key'] = [
            self::DRUPAL_FIELD_ID => 'api_key',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Omnichannel API Key'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('api_key')) ? $config->get('api_key') : ''),
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];

        $form['takeda_id_configure_api']['api_secret'] = [
            self::DRUPAL_FIELD_ID => 'api_secret',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Omnichannel API Secret'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('api_secret')) ? $config->get('api_secret') : ''),
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];

        $language_none = \Drupal::languageManager()->getLanguage(LanguageInterface::LANGCODE_NOT_APPLICABLE);
        $callback_url = Url::fromRoute('takeda_id.lead_callback')
            ->setAbsolute(true)
            ->setOption('language', $language_none)
            ->toString();

        $form['takeda_id_configure_api']['callback_url'] = [
            self::DRUPAL_FIELD_ID => 'callback_url',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Omnichannel Callback URL'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('Your Omnichannel Callback URL for this site. Ensure this is configured by Takeda and accessible on your site without authentication.'),
            self::DRUPAL_FIELD_DEFAULT => $callback_url,
            self::DRUPAL_FIELD_DISABLED => true,
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];

        // Okta Settings
        $form['takeda_id_configure_okta'] = [
            self::DRUPAL_FIELD_TYPE => 'fieldset',
            self::DRUPAL_FIELD_TITLE => $this->t('Okta API Settings')
        ];

        $form['takeda_id_configure_okta']['okta_url'] = [
            self::DRUPAL_FIELD_ID => 'okta_url',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Okta API URL'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('Okta API URL. No trailing slash. Used to register with Single Sign-on'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('okta_url')) ? $config->get('okta_url') : 'https://takedaext.oktapreview.com/oauth2/demfault/v1'),
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];

        $form['takeda_id_configure_okta']['okta_client_id'] = [
            self::DRUPAL_FIELD_ID => 'okta_client_id',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Okta Client ID'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('okta_client_id')) ? $config->get('okta_client_id') : ''),
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];

        $form['takeda_id_configure_okta']['okta_client_secret'] = [
            self::DRUPAL_FIELD_ID => 'okta_client_secret',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Okta Client ID Secret'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('okta_client_secret')) ? $config->get('okta_client_secret') : ''),
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];

        $form['takeda_id_configure_okta']['okta_callback_url'] = [
            self::DRUPAL_FIELD_ID => 'okta_callback_url',
            self::DRUPAL_FIELD_TYPE => 'textfield',
            self::DRUPAL_FIELD_TITLE => $this->t('Okta Callback URL'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('Your Takeda ID / Okta Callback URL for this site. Ensure this is configured by Takeda and accessible on your site without authentication.'),
            self::DRUPAL_FIELD_DEFAULT => $callback_url,
            self::DRUPAL_FIELD_DISABLED => true,
            self::DRUPAL_FIELD_SIZE => 90,
            self::DRUPAL_FIELD_MAXLENGTH => 90
        ];

        // Redirect Settings
        $form['takeda_id_redirects'] = [
            self::DRUPAL_FIELD_TYPE => 'fieldset',
            self::DRUPAL_FIELD_TITLE => $this->t('Takeda ID Redirects')
        ];


        if (!empty($config->get('first_login_redirect'))) {
            $first_redirect = Url::fromRoute($config->get('first_login_redirect')['route_name'], $config->get('first_login_redirect')['route_parameters'])->toString();
        }
        if (!empty($config->get('subsequent_login_redirect'))) {
            $subsequent_redirect = Url::fromRoute($config->get('subsequent_login_redirect')['route_name'], $config->get('subsequent_login_redirect')['route_parameters'])->toString();
        }


        $form['takeda_id_redirects']['first_login_redirect'] = [
            self::DRUPAL_FIELD_ID => 'first_login_redirect',
            self::DRUPAL_FIELD_TYPE => 'path',
            self::DRUPAL_FIELD_TITLE => $this->t('First Login'),
            '#placeholder' => '/user',
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('URL to redirect to on a user\'s first login. For example, you may wish to redirect to an Edit Profile page to complete registration / setup.'),
            // self::DRUPAL_FIELD_DEFAULT => '',
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('first_login_redirect')) ? $first_redirect : ''),
            self::DRUPAL_FIELD_SIZE => 60,
            self::DRUPAL_FIELD_MAXLENGTH => 60
        ];

        $form['takeda_id_redirects']['subsequent_login_redirect'] = [
            self::DRUPAL_FIELD_ID => 'subsequent_login_redirect',
            self::DRUPAL_FIELD_TYPE => 'path',
            self::DRUPAL_FIELD_TITLE => $this->t('Subsequent Login'),
            '#placeholder' => '/',
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('URL to redirect to on a user\'s subsequent logins. For example, your homepage.'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('subsequent_login_redirect')) ? $subsequent_redirect : ''),
            self::DRUPAL_FIELD_SIZE => 60,
            self::DRUPAL_FIELD_MAXLENGTH => 60
        ];


        // Registration Settings
        $form['takeda_id_registration'] = [
            self::DRUPAL_FIELD_TYPE => 'fieldset',
            self::DRUPAL_FIELD_TITLE => $this->t('Takeda ID Account Registration')
        ];

        $country_manager = \Drupal::service('country_manager');
        $countryList = $country_manager->getList();
        // Limit to only Takeda ID supported Countries
        $countryList = array_intersect_key($countryList, array_flip(TakedaIdInterface::SUPPORTED_COUNTRIES));
        $countries = [];
        foreach ($countryList as $country => $name) {
            $val = $name->__toString();
            $countries[$country] = $val;
        }

        // Invalid country if isset as default 
        // users are unable to register and have to be changed.
        $countries = array_merge(['aq' => 'Aardvark'], $countries);
        
        $form['takeda_id_registration']['default_country'] = [
            self::DRUPAL_FIELD_ID => 'default_country',
            self::DRUPAL_FIELD_TYPE => 'select',
            self::DRUPAL_FIELD_OPTIONS => $countries,
            self::DRUPAL_FIELD_TITLE => $this->t('Default Country'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('Takeda ID requires country information to support lead matching. <br/>If your registration form does not explicitly display the <em>field_crm_country</em> field, this default will be used.'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('default_country')) ? $config->get('default_country') : ''),
            // self::DRUPAL_FIELD_SIZE => 90
        ];
        

        $userFields = \Drupal::service('entity_field.manager')->getFieldMap()['user'];
        $availableFields = [];
        $excludedFields = ['uid', 'uuid', 'preferred_admin_langcode', 'name', 'pass', 'status', 'roles', 'created', 'changed', 'access', 'login', 'init', 'default_langcode', 'preferred_langcode', 'field_takeda_id', 'field_last_password_reset', 'field_password_expiration'];
        foreach ($userFields as $fieldName => $value) {
            $availableFields[$fieldName] = $fieldName;
        }
        $availableFields = array_diff($availableFields, $excludedFields);

        $defaultPrefillFields = [];

        $form['takeda_id_registration']['prefill_fields'] = [
            self::DRUPAL_FIELD_ID => 'prefill_fields',
            self::DRUPAL_FIELD_TYPE => 'checkboxes',
            self::DRUPAL_FIELD_OPTIONS => $availableFields,
            self::DRUPAL_FIELD_TITLE => $this->t('Pre-fillable Fields'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('The selected fields on the registration form will be available to pre-prepopulate by using the field name as a query parameter (eg. /user/register?field_first_name=Test&field_last_name=User&mail=test@example.com). Ensure your desired fields have been added to the form in the Drupal configuration. Custom items checked here will also be passed as additional fields to a Takeda ID generated HCP Lead.'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('prefill_fields')) ? $config->get('prefill_fields') : $defaultPrefillFields),
        ];

        $form['takeda_id_registration']['password_management'] = array(
            '#prefix' => '<strong>',
            '#suffix' => '</strong>',
            '#markup' => t('Password Management'),
        );

        $form['takeda_id_registration']['hide_password_policy_table'] = array(
            self::DRUPAL_FIELD_TYPE => 'checkbox',
            self::DRUPAL_FIELD_DEFAULT => (null !== $config->get('hide_password_policy_table') ? $config->get('hide_password_policy_table') : 0),
            self::DRUPAL_FIELD_TITLE => t('Hide Password Policy Table'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('Hide the Password Policy details table on registration / reset password pages. Disable if you wish to style this via your theme or a custom module.')
        );


        $form['takeda_id_registration']['match_header'] = array(
            '#prefix' => '<strong>',
            '#suffix' => '</strong>',
            '#markup' => t('Takeda ID Lead Matching Behaviour'),
        );

        $form['takeda_id_registration']['require_hcp_match_to_activate'] = array(
            self::DRUPAL_FIELD_TYPE => 'checkbox',
            self::DRUPAL_FIELD_DEFAULT => (null !== $config->get('require_hcp_match_to_activate') ? $config->get('require_hcp_match_to_activate') : 0),
            self::DRUPAL_FIELD_TITLE => t('Require valid HCP match before user is activated'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('If enabled, users will not be "activated" in Drupal (and therefore unable to login) until the Takeda ID lead generation process is complete and they\'ve been matched to a valid HCP. <br/>Disable if you wish to customise access for active users with the takeda_id_non_hcp role.')
        );

        // Authorization Component Settings
        $form['takeda_id_authorization'] = [
            self::DRUPAL_FIELD_TYPE => 'fieldset',
            self::DRUPAL_FIELD_TITLE => $this->t('Takeda ID Authorization')
        ];

        $form['takeda_id_authorization']['block_rejected_leads'] = array(
            self::DRUPAL_FIELD_TYPE => 'checkbox',
            self::DRUPAL_FIELD_DEFAULT => (null !== $config->get('block_rejected_leads') ? $config->get('block_rejected_leads') : 1),
            self::DRUPAL_FIELD_TITLE => t('Block rejected leads'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('When enabled, Takeda ID leads rejected as "non-HCP" will be marked as Blocked and unable to login.')
        );

        $form['takeda_id_authorization']['restrict_country_access'] = array(
            self::DRUPAL_FIELD_TYPE => 'checkbox',
            self::DRUPAL_FIELD_DEFAULT => (null !== $config->get('restrict_country_access') ? $config->get('restrict_country_access') : 0),
            self::DRUPAL_FIELD_TITLE => t('Restrict access to approved countries'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('When enabled, Takeda ID accounts can only login if registered in the approved countries.')
        );

        $form['takeda_id_authorization']['restrict_country_access_to_countries'] = [
            self::DRUPAL_FIELD_ID => 'prefill_fields',
            self::DRUPAL_FIELD_TYPE => 'checkboxes',
            self::DRUPAL_FIELD_OPTIONS => $countryList,
            self::DRUPAL_FIELD_TITLE => $this->t('Approved Countries'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('restrict_country_access_to_countries')) ? $config->get('restrict_country_access_to_countries') : [$config->get('default_country')]),
            self::DRUPAL_FIELD_ATTRIBUTES => array('class' => array('country-checkboxes')),
            self::DRUPAL_FIELD_STATES => [
              'visible' => [
                ':input[name=restrict_country_access]' => ['checked' => true],
              ],
            ],
        ];

        // Invitation Settings
        $form['takeda_id_configure_invitations'] = [
            self::DRUPAL_FIELD_TYPE => 'fieldset',
            self::DRUPAL_FIELD_TITLE => $this->t('Takeda ID Invitations')
        ];
        $form['takeda_id_configure_invitations']['invitations_can_edit'] = array(
            self::DRUPAL_FIELD_TYPE => 'checkbox',
            self::DRUPAL_FIELD_DEFAULT => (null !== $config->get('invitations_can_edit') ? $config->get('invitations_can_edit') : 1),
            self::DRUPAL_FIELD_TITLE => t('Allow editing supported invitations'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('When enabled, Takeda ID registrations prefilled with invitation data can be modified by the invited user, as long as the invitation has the appropriate canEdit flag set. <br /> When disabled, account data provided via invitations will always be read only.')
        );

        // Multichannel Settings
        $form['takeda_id_configure_multichannel'] = [
            self::DRUPAL_FIELD_TYPE => 'fieldset',
            self::DRUPAL_FIELD_TITLE => $this->t('Takeda ID Multichannel')
        ];
        $form['takeda_id_configure_multichannel']['multichannel_can_edit'] = array(
            self::DRUPAL_FIELD_TYPE => 'checkbox',
            self::DRUPAL_FIELD_DEFAULT => (null !== $config->get('multichannel_can_edit') ? $config->get('multichannel_can_edit') : 1),
            self::DRUPAL_FIELD_TITLE => t('Allow multichannel'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('When enabled, multichannel activity API will be triggered during login.')
        );

        if (!empty($config->get('first_login_redirect'))) {
            $first_redirect = Url::fromRoute($config->get('first_login_redirect')['route_name'], $config->get('first_login_redirect')['route_parameters'])->toString();
        }
        if (!empty($config->get('subsequent_login_redirect'))) {
            $subsequent_redirect = Url::fromRoute($config->get('subsequent_login_redirect')['route_name'], $config->get('subsequent_login_redirect')['route_parameters'])->toString();
        }


        $form['actions'][self::DRUPAL_FIELD_TYPE] = 'actions';
        $form['actions']['submit'] = [
            self::DRUPAL_FIELD_TYPE => 'submit',
            self::DRUPAL_FIELD_VALUE => $this->t('Save Settings')
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);

        // If restrict access to approved countries is enabled, we want to ensure the current country is valid
        $restrictCountryAccess = $form_state->getValue('restrict_country_access');
        $restrictCountryAccessCountries = [];

        if ($form_state->getValue('restrict_country_access_to_countries')) {
            $restrictCountryAccessCountries = array_keys(array_filter($form_state->getValue('restrict_country_access_to_countries')));
        }

        if ($restrictCountryAccess && !count($restrictCountryAccessCountries)) {
            $form_state->setErrorByName("restrict_country_access", $this->t('Restricting Takeda ID access to approved countries requires at least one country (eg. your default country) to be selected.'));
        }

        if ($restrictCountryAccess && count($restrictCountryAccessCountries) && !in_array($form_state->getValue('default_country'), $restrictCountryAccessCountries)) {
            $form_state->setErrorByName("restrict_country_access_to_countries", $this->t('Restricting Takeda ID access to approved countries requires your default country to be checked to allow single sign on.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->config(TakedaIdInterface::CONFIG_OBJECT_NAME);

        // Set Takeda ID and Omnichannel API Settings
        // Sanity trim the API URL: Remove whitespace and trailing slash
        $api_url = trim($form_state->getValue('api_url'));
        $api_url = rtrim($api_url, "/");

        $lead_api_url = trim($form_state->getValue('lead_api_url'));
        $lead_api_url = rtrim($lead_api_url, "/");

        $invitations_api_url = trim($form_state->getValue('invitations_api_url'));
        $invitations_api_url = rtrim($invitations_api_url, "/");

        $multichannel_api_url = trim($form_state->getValue('multichannel_api_url'));
        $multichannel_api_url = rtrim($multichannel_api_url, "/");

        $config->set('api_url', $api_url);
        $config->set('lead_api_url', $lead_api_url);
        $config->set('invitations_api_url', $invitations_api_url);
        $config->set('multichannel_api_url', $multichannel_api_url);
        $config->set('api_key', trim($form_state->getValue('api_key')));
        $config->set('api_secret', trim($form_state->getValue('api_secret')));

        // Set OKTA API Settings
        $okta_url = trim($form_state->getValue('okta_url'));
        $okta_url = rtrim($okta_url, "/");

        $config->set('okta_url', $okta_url);
        $config->set('okta_client_id', $form_state->getValue('okta_client_id'));
	    $config->set('okta_client_secret', $form_state->getValue('okta_client_secret'));

        // Set Takeda ID Redirect Settings
        $config->set('first_login_redirect', $form_state->getValue('first_login_redirect'));
        $config->set('subsequent_login_redirect', $form_state->getValue('subsequent_login_redirect'));

        $config->set('customer_id_name', $form_state->getValue('customer_id_name'));
        $config->set('customer_id_description', $form_state->getValue('customer_id_description'));
        $config->set('customer_id_placeholder', $form_state->getValue('customer_id_placeholder'));

        // Set Account Registration Settings
        $config->set('default_country', $form_state->getValue('default_country'));

        $prefillFields = array_keys(array_filter($form_state->getValue('prefill_fields')));
        $config->set('prefill_fields', $prefillFields);
        $config->set('require_hcp_match_to_activate', $form_state->getValue('require_hcp_match_to_activate'));
        $config->set('hide_password_policy_table', $form_state->getValue('hide_password_policy_table'));

        // Set Authorization Configuration
        $config->set('block_rejected_leads', $form_state->getValue('block_rejected_leads'));

        $config->set('restrict_country_access', $form_state->getValue('restrict_country_access'));

        if ($form_state->getValue('restrict_country_access_to_countries')) {
            $restrictCountryAccessCountries = array_keys(array_filter($form_state->getValue('restrict_country_access_to_countries')));
            $config->set('restrict_country_access_to_countries', $restrictCountryAccessCountries);
        } else {
            $config->set('restrict_country_access_to_countries', []);
        }

        // Set Invitations Configuration
        $config->set('invitations_can_edit', $form_state->getValue('invitations_can_edit'));

        $config->set('multichannel_can_edit', $form_state->getValue('multichannel_can_edit'));

        $config->save();
        return parent::submitForm($form, $form_state);
    }
}
