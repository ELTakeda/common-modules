<?php
namespace Drupal\spotme_events\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class MuleSoftSpotMeApiConfigForm extends ConfigFormBase {

  const MULESOFT_SPOT_ME_API_CONFIG_NAME = 'spotme.mulesoft_api_config';

  /**
   * @return string[]
   */
  protected function getEditableConfigNames() {
    return [
      self::MULESOFT_SPOT_ME_API_CONFIG_NAME,
    ];
  }

  /**
   * @return string
   */
  public function getFormId() {
    return 'spot_me_custom_mulesoft_api_config';
  }

  /**
   * Implement function buildForm.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::MULESOFT_SPOT_ME_API_CONFIG_NAME);

    $form['url_endpoint'] = [
      '#id' => 'url_endpoint',
      '#type' => 'textfield',
      '#title' => $this->t('URL endpoint'),
      '#default_value' => (!empty($config->get('url_endpoint')) ? $config->get('url_endpoint') : ''),
      '#size' => 60,
      '#required' => TRUE,
    ];

    $form['client_id'] = [
      '#id' => 'client_id',
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#default_value' => (!empty($config->get('client_id')) ? $config->get('client_id') : ''),
      '#size' => 60,
      '#required' => TRUE,
    ];

    $form['client_secret'] = [
      '#id' => 'client_secret',
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#default_value' => (!empty($config->get('client_secret')) ? $config->get('client_secret') : ''),
      '#size' => 60,
      '#required' => TRUE,
    ];

    $form['webinar_provider'] = [
      '#id' => 'webinar_provider',
      '#type' => 'textfield',
      '#title' => $this->t('Webinar provider'),
      '#default_value' => (!empty($config->get('webinar_provider')) ? $config->get('webinar_provider') : ''),
      '#size' => 60,
      '#required' => TRUE,
    ];

    $form['record_type'] = [
      '#id' => 'record_type',
      '#type' => 'textfield',
      '#title' => $this->t('Record Type'),
      '#default_value' => (!empty($config->get('record_type')) ? $config->get('record_type') : ''),
      '#size' => 60,
      '#required' => TRUE,
    ];

    $form['url_endpoint_magic_link'] = [
      '#id' => 'url_endpoint',
      '#type' => 'textfield',
      '#title' => $this->t('URL endpoint Magic link'),
      '#default_value' => (!empty($config->get('url_endpoint_magic_link')) ? $config->get('url_endpoint_magic_link') : ''),
      '#size' => 60,
      '#required' => TRUE,
    ];

    $form['client_id_magic_link'] = [
      '#id' => 'client_id',
      '#type' => 'textfield',
      '#title' => $this->t('Client ID Magic link'),
      '#default_value' => (!empty($config->get('client_id_magic_link')) ? $config->get('client_id_magic_link') : ''),
      '#size' => 60,
      '#required' => TRUE,
    ];

    $form['client_secret_magic_link'] = [
      '#id' => 'client_secret',
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret Magic link'),
      '#default_value' => (!empty($config->get('client_secret_magic_link')) ? $config->get('client_secret_magic_link') : ''),
      '#size' => 60,
      '#required' => TRUE,
    ];

    $form['org_id'] = [
      '#id' => 'org_id',
      '#type' => 'textfield',
      '#title' => $this->t('Org ID'),
      '#default_value' => (!empty($config->get('org_id')) ? $config->get('org_id') : ''),
      '#size' => 60,
      '#required' => TRUE,
    ];

    $form['webinar_system'] = [
      '#id' => 'webinar_system',
      '#type' => 'textfield',
      '#title' => $this->t('Webinar system'),
      '#default_value' => (!empty($config->get('webinar_system')) ? $config->get('webinar_system') : ''),
      '#size' => 60,
      '#required' => TRUE,
    ];

    $form['register_event_success_template_subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Successful event registration email subject'),
      '#default_value' => (!empty($config->get('register_event_success_template_subject')) ? $config->get('register_event_success_template_subject') : ''),
      '#required' => TRUE,
    ];
    $form['register_event_success_template_body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Successful event registration email body'),
      '#default_value' => (!empty($config->get('register_event_success_template_body')) ? $config->get('register_event_success_template_body') : ''),
      '#rows' => 20,
      '#required' => TRUE,
    ];

    $form['register_event_fail_template_subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Failed event registration email subject'),
      '#default_value' => (!empty($config->get('register_event_fail_template_subject')) ? $config->get('register_event_fail_template_subject') : ''),
      '#required' => TRUE,
    ];
    $form['register_event_fail_template_body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Failed event registration email body'),
      '#default_value' => (!empty($config->get('register_event_fail_template_body')) ? $config->get('register_event_fail_template_body') : ''),
      '#rows' => 20,
      '#required' => TRUE,
    ];
    $form['upcoming_event_before_day'] = [
      '#type' => 'number',
      '#title' => $this->t('Show Upcoming Events before day'),
      '#default_value' => !empty($config->get('upcoming_event_before_day')) ? $config->get('upcoming_event_before_day') : 30,
      '#min' => '1',
      '#required' => TRUE,
    ];
    $form['hide_past_events'] = [
      '#type' => 'checkbox',
      '#title' => 'Hide Past Events',
      '#default_value' => $config->get('hide_past_events') ?? 0,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Implement function submitForm.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config(self::MULESOFT_SPOT_ME_API_CONFIG_NAME);
    $config->set('url_endpoint', $form_state->getValue('url_endpoint'));
    $config->set('client_id', $form_state->getValue('client_id'));
    $config->set('client_secret', $form_state->getValue('client_secret'));
    $config->set('url_endpoint_magic_link', $form_state->getValue('url_endpoint_magic_link'));
    $config->set('client_id_magic_link', $form_state->getValue('client_id_magic_link'));
    $config->set('client_secret_magic_link', $form_state->getValue('client_secret_magic_link'));
    $config->set('org_id', $form_state->getValue('org_id'));
    $config->set('record_type', $form_state->getValue('record_type'));
    $config->set('webinar_system', $form_state->getValue('webinar_system'));
    $config->set('webinar_provider', $form_state->getValue('webinar_provider'));
    $config->set('register_event_fail_template_subject', $form_state->getValue('register_event_fail_template_subject'));
    $config->set('register_event_fail_template_body', $form_state->getValue('register_event_fail_template_body'));
    $config->set('register_event_success_template_subject', $form_state->getValue('register_event_success_template_subject'));
    $config->set('register_event_success_template_body', $form_state->getValue('register_event_success_template_body'));
    $config->set('hide_past_events', $form_state->getValue('hide_past_events'));
    $config->set('upcoming_event_before_day', $form_state->getValue('upcoming_event_before_day'));
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
