<?php

namespace Drupal\custom_redirects\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin configuration form.
 */
class CustomRedirectsConfigureForm extends ConfigFormBase
{
    /**
     * Determines the ID of a form.
     */
    public function getFormId()
    {
        return 'custom_redirects_config_form_configure';
    }

    /**
     * Gets the configuration names that will be editable.
     */
    public function getEditableConfigNames()
    {
        return [
            'custom_redirects_config_form.settings'
        ];
    }

    /**
     * Form constructor.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Get the saved settings.
        $config = $this->config('custom_redirects_config_form.settings');
        $custom_redirects_config_form_values = $config->get('custom_redirects_config_form_values') ?: []; // Blank array if no data so that it can be countable

        // Create the fieldset that contains all fields.
        $form['custom_redirects_settings'] = [
            '#type' => 'fieldset',
            '#title' => 'Deep Link Redirects Data',
            '#tree' => true
        ];  

        $form['custom_redirects_settings']['custom_redirects_landing'] = [
            '#type' => 'textfield',
            '#title' => 'Landing page',
            '#description' => 'URL to the landing page',
            '#default_value' => !empty($custom_redirects_config_form_values['custom_redirects_landing']) ? $custom_redirects_config_form_values['custom_redirects_landing'] : ''
        ];

        $form['custom_redirects_settings']['custom_redirects_login'] = [
            '#type' => 'textfield',
            '#title' => 'Login',
            '#description' => 'Overwrites the /user/login route to be redirected to the defined one',
            '#default_value' => !empty($custom_redirects_config_form_values['custom_redirects_login']) ? $custom_redirects_config_form_values['custom_redirects_login'] : ''
        ];

        $form['custom_redirects_settings']['custom_redirects_register'] = [
            '#type' => 'textfield',
            '#title' => 'Register',
            '#description' => 'Overwrites the /user/register route to be redirected to the defined one',
            '#default_value' => !empty($custom_redirects_config_form_values['custom_redirects_register']) ? $custom_redirects_config_form_values['custom_redirects_register'] : ''
        ];

        $form['custom_redirects_settings']['custom_redirects_profile'] = [
            '#type' => 'textfield',
            '#title' => 'Profile / User',
            '#description' => 'Overwrites the /user/X route to be redirected to the defined one',
            '#default_value' => !empty($custom_redirects_config_form_values['custom_redirects_profile']) ? $custom_redirects_config_form_values['custom_redirects_profile'] : ''
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
        $values = $form_state->getValue(['custom_redirects_settings']);
        $config = \Drupal::service('config.factory')->getEditable('custom_redirects_config_form.settings');
        $config->set('custom_redirects_config_form_values', $values);
        $config->save();
        return parent::submitForm($form, $form_state);
    }
}