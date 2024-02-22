<?php

namespace Drupal\spotme_events\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\spotme_events\Ajax\ReloadPageCommandSpotme;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;

/**
 * Provides the OpenID Connect login form.
 */
class RegisterEventPendingForm extends FormBase {



  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'register_event_pending_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['text_custom'] = [
      '#markup' => "<p>Your registration has been received. We will soon let you know if the registration is successful via email within 24 hours.</p>",
    ];
    $form['actions']['btn_submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Close'),
      '#name' => 'btn_submit',
      '#attributes' => [
        'class' => [
          'btn',
          'btn-yes',
          'btn-reload-page',
        ],
      ],
    );

    $form['#attached']['library'][] = 'spotme_events/spotme_events.library';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function submitModalFormAjax(array $form, FormStateInterface $form_state): AjaxResponse {
    $command = new ReloadPageCommandSpotme();
    $response = new AjaxResponse();
    $response->addCommand($command);
    return $response;
  }

}
