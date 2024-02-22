<?php

namespace Drupal\takeda_brightcove\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/** 
 * Configure example settings for this site.
 */
class BrightcoveConfigureForm extends ConfigFormBase {

    /**
     * Config settings.
     *
     * @var string
     */
    const SETTINGS = 'takeda_brightcove.settings';

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'takeda_brightcove_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return [
            static::SETTINGS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = $this->config(static::SETTINGS);

        $form['takeda_brightcove_configure'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Takeda Brightcove Player Setup')
        ];

        $form['takeda_brightcove_configure']['account_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Account ID'),
            '#default_value' => $config->get('account_id'),
            '#description' => $this->t('The Brightcove Account ID associated with your Brightcove Studio account. <br/>This is displayed within the main menu on Brightcove\'s Video Cloud page.')
        ];

        $form['takeda_brightcove_configure']['policy_key'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Policy Key'),
            '#default_value' => $config->get('policy_key'),
            '#maxlength' => 250,
            '#description' => $this->t('The Brightcove Policy Key is used to retrieve thumbnails and metadata from Brightcove. <br/>You can find the Policy Key for your Brightcove Player from the "JSON Editor" in the player properties. <br/>Refer to the <a href="https://apis.support.brightcove.com/policy/getting-started/policy-keys.html" target="_blank">Brightcove Documentation</a> for further details.')
        ];


        $form['takeda_brightcove_configure']['player_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Player ID'),
            '#default_value' => $config->get('player_id'),
            '#description' => $this->t('The Brightcove Player used to render media. You can find the player ID from within the Players module in Video Cloud, or extract it from your embed url. Use <em>default</em> for the Default brightcove player.')
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this->configFactory->getEditable(static::SETTINGS)
            // Update the module configuration
            ->set('account_id', $form_state->getValue('account_id'))
            ->set('policy_key', $form_state->getValue('policy_key'))
            ->set('player_id', $form_state->getValue('player_id'))
            ->save();

        parent::submitForm($form, $form_state);
    }

}
