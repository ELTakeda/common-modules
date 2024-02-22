<?php

namespace Drupal\spotme_events\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\spotme_events\Ajax\ReloadPageCommandSpotme;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;

/**
 * Provides the OpenID Connect login form.
 */
class RegisterEventSuccessForm extends FormBase {



  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'register_event_success_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['text_custom'] = [
      '#markup' => "<p>Thanks for registering! We've reserved your space.</p>",
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

}
