<?php

namespace Drupal\spotme_events\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\spotme_events\Ajax\ReloadPageCommandSpotme;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;

/**
 * Provides the OpenID Connect login form.
 */
class RegisterEventForm extends FormBase {



  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'register_event_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['text_custom'] = [
      '#markup' => '<p>Do you want to register this event ?</p>',
    ];
    // Btn submit modal.
    $form['actions']['btn_close'] = array(
      '#type' => 'button',
      '#value' => $this->t('No'),
      '#name' => 'btn_close',
      '#attributes' => [
        'class' => [
          'btn',
          'btn-close',
          'btn-no',
        ],
      ],
    );
    // Btn submit.
    $form['actions']['btn_submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Yes'),
      '#name' => 'btn_submit',
      '#attributes' => [
        'class' => [
          'btn',
          'btn-yes',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'submitModalFormAjax'],
        'event' => 'click',
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
    if (\Drupal::currentUser()->isAuthenticated()) {
      $user = User::load(\Drupal::currentUser()->id());
      $registered_events = $user->get('field_registered_event')->getValue() ?? [];
      $config = \Drupal::service('config.factory')->getEditable('spotme.register_event_config');
      $data_register = $config->get('register_data') ?? [];

      // User data.
      $data = [
        "takedaId" => $user->field_takeda_id->value,
        "email" => $user->mail->value,
        "attendeeStatus" => "active",
      ];
      if ($user->field_first_name->value) {
        $data['firstName'] = $user->field_first_name->value;
      }
      if ($user->field_last_name->value) {
        $data['lastName'] = $user->field_last_name->value;
      }

      // Load event from URL.
      $event = Node::load(\Drupal::routeMatch()->getParameter('event_nid'));
      // Set timezone.
      if (isset(\Drupal::config('system.date')->get('timezone')['default'])) {
        date_default_timezone_set(\Drupal::config('system.date')->get('timezone')['default']);
      }
      $datetime1 = time();
      $datetime2 = strtotime($event->field_start_at->value);
      $minutes_to_add = 10;
      $config_mule = \Drupal::service('config.factory')->getEditable('spotme.mulesoft_api_config');
      $webinar_system = $config_mule->get('webinar_system');
      $webinar_provider = $config_mule->get('webinar_provider');
      $record_type = $config_mule->get('record_type');
      $org_id = $config_mule->get('org_id');
      // Data MCA.
      $data_mca = [
        'webinarProvider' => $webinar_provider,
        'recordTypeName' => $record_type,
        'webinarId' => \Drupal::routeMatch()->getParameter('webinar_id'),
        'webinarDatetime' => gmdate('Y-m-d\TH:i:s.000\Z', strtotime($event->field_start_at->value)),
        'registrationDatetime' => gmdate('Y-m-d\TH:i:s.000\Z'),
        'webinarName' => $event->title->value,
        'countryCode' => $user->field_crm_country->value,
        'takedaEnterpriseId' => $user->field_customer_id->value,
        'magicLink' => '',
        'takeda_id' => $user->field_takeda_id->value,
      ];
      $data_push_to_api['data_mca'][] = $data_mca;
      if (($datetime2 - $datetime1) < ($minutes_to_add * 60) ) {
        $registered_events[] = [
          'target_id' => \Drupal::routeMatch()->getParameter('event_nid'),
          'value' => date('Y-m-d\TH:i:s.000\Z'),
        ];
        $user->set('field_registered_event', $registered_events)->save();
        $data_push_to_api['data_crm']['registerAttendees'] = [
          'orgId' => $org_id,
          'webinarSystem' => $webinar_system,
          'webinarId' => \Drupal::routeMatch()->getParameter('webinar_id'),
          'attendeesList' => [$data],
          'nid' => \Drupal::routeMatch()->getParameter('event_nid'),
        ];

        // Call service API.
        $response = \Drupal::service('spotme_events_service')->registerEvent($data_push_to_api);

        if (isset($response[0]['webinarId']) && $response[0]['webinarId']) {
          // Show popup register success.
          $response = new AjaxResponse();
          $modal_form = \Drupal::formBuilder()->getForm('Drupal\spotme_events\Form\RegisterEventSuccessForm');
          $options = [
            'width' => '324px',
            'dialogClass' => 'line-popup',
          ];
          $response->addCommand(new OpenModalDialogCommand('Register Event Success', $modal_form, $options));
          return $response;
        }
        else {
          // Show popup register fail.
          $response = new AjaxResponse();
          $modal_form = \Drupal::formBuilder()->getForm('Drupal\spotme_events\Form\RegisterEventFailForm');
          $options = [
            'width' => '324px',
            'dialogClass' => 'line-popup',
          ];
          $response->addCommand(new OpenModalDialogCommand('Register Event Fail', $modal_form, $options));
          return $response;
        }
      }
      else {
        $data_register[] = [
          'webinarId' => \Drupal::routeMatch()->getParameter('webinar_id'),
          'user_data' => $data,
          'data_mca' => $data_mca,
          'nid' => \Drupal::routeMatch()->getParameter('event_nid'),
        ];
        $registered_events[] = [
          'target_id' => \Drupal::routeMatch()->getParameter('event_nid'),
          'value' => date('Y-m-d\TH:i:s.000\Z'),
        ];
        $user->set('field_registered_event', $registered_events)->save();
        $config->set('register_data', $data_register)->save();

        // Show popup register pending.
        $response = new AjaxResponse();
        $modal_form = \Drupal::formBuilder()->getForm('Drupal\spotme_events\Form\RegisterEventPendingForm');
        $options = [
          'width' => '324px',
          'dialogClass' => 'line-popup',
        ];
        $response->addCommand(new OpenModalDialogCommand('Register Event Pending', $modal_form, $options));
        return $response;
      }
    }
    // Show popup register fail.
    $response = new AjaxResponse();
    $modal_form = \Drupal::formBuilder()->getForm('Drupal\spotme_events\Form\RegisterEventFailForm');
    $options = [
      'width' => '324px',
      'dialogClass' => 'line-popup',
    ];
    $response->addCommand(new OpenModalDialogCommand('Register Event Fail', $modal_form, $options));
    return $response;
  }

}
