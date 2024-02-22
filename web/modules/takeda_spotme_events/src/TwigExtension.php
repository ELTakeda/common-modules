<?php

namespace Drupal\spotme_events;

use DateTime;
use Drupal\line_custom\Form\LineConnectForm;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\user\Entity\User;
use Drupal\views\Views;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension {

  /**
   * The module handler to invoke alter hooks.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The theme manager to invoke alter hooks.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('getListViewWebinar', [$this, 'getListViewWebinar']),
      new TwigFunction('getCurrentPath', [$this, 'getCurrentPath']),
      new TwigFunction('getCurrentUser', [$this, 'getCurrentUser']),
      new TwigFunction('checkRegisteredEvent', [$this, 'checkRegisteredEvent']),
      new TwigFunction('renderField', [$this, 'renderField']),
      new TwigFunction('checkUserCanRegistered', [$this, 'checkUserCanRegistered']),
      new TwigFunction('checkEventState', [$this, 'checkEventState']),
      new TwigFunction('getSpotMeConfigColor', [$this, 'getSpotMeConfigColor']),
      new TwigFunction('getUrlFromDestination', [$this, 'getUrlFromDestination']),
      new TwigFunction('isHidePastEvents', [$this, 'isHidePastEvents']),
    ];
  }

  /**
   * {@inheritdoc }
   */
  public static function getListViewWebinar($type = 'page') {
    $list_views_webinar = [];

    $views = Views::getView('webinar_event');
    $views_display = $views->storage->get('display');

    if ($views_display) {
      if (isset($views_display['default'])) {
        unset($views_display['default']);
      }
      foreach ($views_display as $key => $view) {
        if ($type == 'page') {
          if (!isset($view['display_options']['path'])) {
            unset($views_display[$key]);
          }
        }
        else {
          if (isset($view['display_options']['path'])) {
            unset($views_display[$key]);
          }
        }
        if (isset($view['display_options']['enabled']) && !$view['display_options']['enabled']) {
          unset($views_display[$key]);
        }
      }
      $position = array_column($views_display, 'position');
      array_multisort($position, SORT_ASC, $views_display);
      $list_views_webinar = $views_display;
    }
    return $list_views_webinar;
  }

  public static function getCurrentPath() {
    $path = \Drupal::service('path.current')->getPath();
    // $query_params = \Drupal::request()->query->all();
    // if ($query_params) {
    //   $index = 0;
    //   foreach ($query_params as $key => $query) {
    //     if ($index == 0) {
    //       $path .= '?' . $key . '=' . $query;
    //     }
    //     else {
    //       $path .= '&' . $key . '=' . $query;
    //     }
    //     $index++;
    //   }
    // }
    return $path;
  }

  public static function getCurrentUser() {
    return User::load(\Drupal::currentUser()->id());
  }

  public static function checkRegisteredEvent($user, $node_event_id) {
    if (!$user) {
      $user = User::load(\Drupal::currentUser()->id());
    }
    $registered = 0;

    $registered_events = $user->get('field_registered_event')->getValue();
    $key = array_search($node_event_id, array_column($registered_events, 'target_id'));
    if ($key || $key === 0){
      $registered = 1;
    }

    return $registered;
  }

  /**
   * {@inheritdoc }
   */
  public static function renderField($entity, $field_name = '') {
    if (is_object($entity)) {
      return @\Drupal::service('renderer')->renderPlain($entity->{$field_name}->view(['type' => 'entity_reference_entity_view', 'label' => 'hidden']));
    }
    return '';
  }

  /**
   * {@inheritdoc }
   */
  public static function checkUserCanRegistered($user) {
    if (!$user) {
      $user = User::load(\Drupal::currentUser()->id());
    }
    $canRegister = 0;
    if ($user->field_customer_id->value) {
      $canRegister = 1;
    }
    return $canRegister;
  }

  public static function checkEventState($event) {
    $config = \Drupal::config('spotme.mulesoft_api_config');
    $upcoming_event_before_day = $config->get('upcoming_event_before_day') ?? '30';
    $state = 'new_arrival';
    $now = strtotime(date('Y/m/d H:i'));
    if (isset(\Drupal::config('system.date')->get('timezone')['default'])) {
      date_default_timezone_set(\Drupal::config('system.date')->get('timezone')['default']);
      $now = strtotime(date('Y/m/d H:i'));
    }
    $dateTime = new DateTime(date('Y/m/d H:i', strtotime($event->field_start_at->value)));
    $dateTime->modify('+' . $event->field_duration_in_minites->value . ' minutes');
    $event_end_time = strtotime($dateTime->format("Y/m/d H:i"));
    $event_start_at_minus_day = $dateTime = new DateTime(date('Y/m/d H:i', strtotime($event->field_start_at->value)));
    $event_start_at_minus_day->modify('-' . $upcoming_event_before_day . ' day');
    if ($event_end_time < $now) {
      $state = 'past';
    }
    elseif ($event_end_time > $now && strtotime($event->field_start_at->value) < $now) {
      $state = 'live';
    }
    elseif (strtotime($event->field_start_at->value) > $now && strtotime($event_start_at_minus_day->format("Y/m/d H:i")) <= $now) {
      $state = 'comming_soon';
    }
    return $state;
  }

  public static function getSpotMeConfigColor() {
    $config = \Drupal::config('spotme.color_config');
    return [
      'tab_background_color' => $config->get('tab_background_color'),
      'tab_hover_background_color'  => $config->get('tab_hover_background_color'),
      'border_color' => $config->get('border_color'),
      'tab_text_color' => $config->get('tab_text_color'),
      'tab_text_hover_color' => $config->get('tab_text_hover_color'),
      'tab_title_color' => $config->get('tab_title_color'),
      'item_time_background_color' => $config->get('item_time_background_color'),
      'item_time_text_color' => $config->get('item_time_text_color'),
      'item_title_text_color' => $config->get('item_title_text_color'),
      'item_title_text_hover_color' => $config->get('item_title_text_hover_color'),
      'item_button_background_color' => $config->get('item_button_background_color'),
      'item_button_background_hover_color' => $config->get('item_button_background_hover_color'),
      'item_button_text_color' => $config->get('item_button_text_color'),
      'item_button_text_hover_color' => $config->get('item_button_text_hover_color'),
      'message_text_color' => $config->get('message_text_color'),
      'popup_button_background_color' => $config->get('popup_button_background_color'),
      'popup_button_background_hover_color' => $config->get('popup_button_background_hover_color'),
      'popup_button_text_color' => $config->get('popup_button_text_color'),
      'popup_button_text_hover_color' => $config->get('popup_button_text_hover_color'),
      'popup_button_yes_background_color' => $config->get('popup_button_yes_background_color'),
      'popup_button_yes_background_hover_color' => $config->get('popup_button_yes_background_hover_color'),
      'popup_button_yes_text__color' => $config->get('popup_button_yes_text__color'),
      'popup_button_yes_text_hover_color' => $config->get('popup_button_yes_text_hover_color'),
    ];
  }

  public static function getUrlFromDestination() {
    return \Drupal::request()->query->get('destination');
  }

  public static function isHidePastEvents() {
    $hide = 0;
    $config = \Drupal::config('spotme.mulesoft_api_config');
    if ($config->get('hide_past_events')) {
      $hide = 1;
    }
    return $hide;
  }

}
