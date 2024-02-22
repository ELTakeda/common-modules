<?php

namespace Drupal\custom_country_info\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin configuration form.
 */
class CustomCountryInformationConfigureForm extends ConfigFormBase
{
    /**
     * Determines the ID of a form.
     */
    public function getFormId()
    {
        return 'custom_country_info_configure';
    }

    /**
     * Gets the configuration names that will be editable.
     */
    public function getEditableConfigNames()
    {
        return [
            'custom_country_info.settings'
        ];
    }

    /**
     * Form constructor.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Get the saved settings.
        $config = $this->config('custom_country_info.settings');
        $custom_country_info_values = $config->get('custom_country_info_values') ?: []; // Blank array if no data so that it can be countable

        // Creathe the fieldset that contains all fields.
        $form['custom_country_info_settings'] = [
            '#type' => 'fieldset',
            '#title' => 'Country information fields',
            '#tree' => true
        ];        

        $form['custom_country_info_settings']['country_codes'] = [
            '#type' => 'text_format',
            '#format' => 'basic_html',
            '#basetype' => 'textarea',
            '#title' => 'Country codes',
            '#default_value' => !empty($custom_country_info_values['country_codes']['value']) ? $custom_country_info_values['country_codes']['value'] : ''
        ];     
        

        // Form submit button
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => 'Save Settings',
            '#button_type' => 'primary'
        ];
       
        return $form;
    }

    /**
     * {@inheritdoc}
     */

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $values = $form_state->getValue(['custom_country_info_settings']);
        $config = \Drupal::service('config.factory')->getEditable('custom_country_info.settings');
        $config->set('custom_country_info_values', $values);
        $config->save();
        return parent::submitForm($form, $form_state);
    }
}