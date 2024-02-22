<?php

namespace Drupal\spotme_events\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SpotMeCustomController extends ControllerBase {

  /**
   * {@inheritdoc }
   */
  public function registerEvent(Request $request) {
    $response = new AjaxResponse();
    $modal_form = $this->formBuilder()->getForm('Drupal\spotme_events\Form\RegisterEventForm');
    $options = [
      'width' => '324px',
      'dialogClass' => 'line-popup',
    ];
    $response->addCommand(new OpenModalDialogCommand('Register Event', $modal_form, $options));
    return $response;
  }

  /**
   * {@inheritdoc }
   */
  public function viewLiveEvent(Request $request) {
    if (\Drupal::currentUser()->isAuthenticated()
      || \Drupal::routeMatch()->getParameter('event_nid')
      || \Drupal::routeMatch()->getParameter('webinar_id')) {
      $user = User::load(\Drupal::currentUser()->id());
      $event_id = \Drupal::routeMatch()->getParameter('event_nid');
      $webinar_id = \Drupal::routeMatch()->getParameter('webinar_id');
      $registered_events = $user->get('field_registered_event')->getValue();
      $key = array_search($event_id, array_column($registered_events, 'target_id'));
      if (!$key && $key !== 0) {
        //throw new AccessDeniedHttpException();
      }

      $event_link = \Drupal::service('spotme_events_service')->getLinkEvent(['webinarId' => $webinar_id]);
      if (isset($event_link['loginURL']) && $event_link['loginURL']) {
        $response = new RedirectResponse($event_link['loginURL']);
        $response->send();
        exit();
      }
      else {
        throw new AccessDeniedHttpException();
      }

    }
    else {
      throw new AccessDeniedHttpException();
    }

  }

}
