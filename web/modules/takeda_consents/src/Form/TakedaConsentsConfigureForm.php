<?php

namespace Drupal\takeda_consents\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\Core\Url;
use Drupal\takeda_id\TakedaIdInterface;

/**
 * Plugin configuration form.
 */
class TakedaConsentsConfigureForm extends ConfigFormBase
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
        return 'takeda_consent_configure';
    }

    /**
     * Gets the configuration names that will be editable.
     */
    public function getEditableConfigNames()
    {
        return [
            'consents.settings'
        ];
    }

    /**
     * Form constructor.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Read Settings.
        $config = $this->config('consents.settings');

        // Consent Settings
        $form['takeda_id_consent'] = [
            self::DRUPAL_FIELD_TYPE => 'fieldset',
            self::DRUPAL_FIELD_TITLE => $this->t('Takeda Consent Reporting')
        ];

        $userFields = \Drupal::service('entity_field.manager')->getFieldMap()['user'];
        $availableFields = [];
        $excludedFields = ['uid', 'uuid', 'preferred_admin_langcode', 'name', 'pass', 'status', 'roles', 'created', 'changed', 'access', 'login', 'init', 'default_langcode', 'preferred_langcode', 'field_takeda_id', 'field_last_password_reset', 'field_password_expiration'];
        foreach ($userFields as $fieldName => $value) {
            $availableFields[$fieldName] = $fieldName;
        }
        $availableFields = array_diff($availableFields, $excludedFields);

        $availablePhoneFields = [];
        $defaultPhoneField = [];
        foreach ($userFields as $fieldName => $value) {
            if (!in_array($value['type'], ['boolean', 'language', 'uuid', 'email', 'image', 'datetime', 'list_string']) && !in_array($fieldName, ['timezone', 'field_pending_expire_sent', 'field_first_name', 'field_last_name', 'field_local_id', 'field_customer_id'])) {
                $availablePhoneFields[$fieldName] = $fieldName;
            }
            if ($value['type'] == 'telephone') {
                $defaultPhoneField = $fieldName;
            }
        }
        $availablePhoneFields = array_diff($availablePhoneFields, $excludedFields);

        $form['takeda_id_consent']['countries_list_gem'] = [
            '#type' => 'textarea',
            '#title' => $this->t('GEM countries'),
            '#maxlength' => 80000,
            '#size' => 5, 
            '#rows' => 10,
            '#description' => $this->t('Separate codes and labels with a "|". Every item(code|label) must be on new line.'),
            '#default_value' => !empty($config->get('gemCountries')) ? $config->get('gemCountries') : ''
        ];
        $form['takeda_id_consent']['countries_list_eucan'] = [
            '#type' => 'textarea',
            '#title' => $this->t('EUCAN countries'),
            '#maxlength' => 80000,
            '#size' => 5, 
            '#rows' => 10,
            '#description' => $this->t('Separate codes and labels with a "|". Every item(code|label) must be on new line.'),
            '#default_value' => !empty($config->get('eucanCountries')) ? $config->get('eucanCountries') : ''
        ];

        $form['takeda_id_consent']['enable_takeda_id_consent'] = array(
            self::DRUPAL_FIELD_TYPE => 'checkbox',
            self::DRUPAL_FIELD_DEFAULT => (null !== $config->get('enable_takeda_id_consent') ? $config->get('enable_takeda_id_consent') : 0),
            self::DRUPAL_FIELD_TITLE => t('Enable Takeda ID Consent Reporting'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('When enabled, user consent will be captured at registration and submitted to the Takeda CRM on successful account activation.')
        );

        $form['takeda_id_consent']['consent_channels'] = [
            self::DRUPAL_FIELD_ID => 'consent_channels',
            self::DRUPAL_FIELD_TYPE => 'checkboxes',
            self::DRUPAL_FIELD_OPTIONS => [
                'Marketing_Email_TPI' => $this->t('Marketing Email'),
                'TPI_Text_SMS' => $this->t('Text/SMS'),
            ],
            self::DRUPAL_FIELD_TITLE => $this->t('Consent Channels'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('Depending on local requirements, consent may need to be captured in the CRM for Email, Phone/Text, or both. Select the appropriate consent channels for your requirements.'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('consent_channels')) ? $config->get('consent_channels') : []),
            self::DRUPAL_FIELD_STATES => [
                'visible' => [
                ':input[name=enable_takeda_id_consent]' => ['checked' => true],
                ],
            ],
        ];

        $form['takeda_id_consent']['consent_phone_field'] = [
            self::DRUPAL_FIELD_ID => 'consent_phone_field',
            self::DRUPAL_FIELD_TYPE => 'select',
            self::DRUPAL_FIELD_OPTIONS => $availablePhoneFields,
            self::DRUPAL_FIELD_TITLE => $this->t('Phone Number Field'),
            self::DRUPAL_FIELD_DESCRIPTION => $this->t('The field used to capture the user\'s phone number. Enable the core Drupal Telephone module and configure a Telephone field in the <a href="/admin/config/people/accounts/fields">Account Fields settings</a> for best results.'),
            self::DRUPAL_FIELD_DEFAULT => (!empty($config->get('consent_phone_field')) ? $config->get('consent_phone_field') : $defaultPhoneField),
            self::DRUPAL_FIELD_STATES => [
                'visible' => [
                ':input[name*=TPI_Text_SMS]' => ['checked' => true],
                ],
            ],
        ];


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
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->config('consents.settings');
    
        $gemCountries = $form_state->getValue(['countries_list_gem']);
        $eucanCountries = $form_state->getValue(['countries_list_eucan']);

        // Set Consent Configuration
        $config->set('enable_takeda_id_consent', $form_state->getValue('enable_takeda_id_consent'));
        $config->set('consent_field', $form_state->getValue('consent_field'));

        if ($form_state->getValue('consent_channels')) {
            $consentChannels = array_keys(array_filter($form_state->getValue('consent_channels')));
            $config->set('consent_channels', $consentChannels);
        } else {
            $config->set('consent_channels', []);
        }

        $config->set('consent_phone_field', $form_state->getValue('consent_phone_field'));
        $config->set('gemCountries', $gemCountries);
        $config->set('eucanCountries', $eucanCountries);

        $config->save();
        return parent::submitForm($form, $form_state);
    }
}