<?php
namespace Drupal\spotme_events\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SpotMeConfigColor extends ConfigFormBase {

  const SPOTME_CONFIG_COLOR = 'spotme.color_config';
  const START_COLOR = '#FFFFFF';
  const END_COLOR = '#000000';
  const HASH_COLOR_OPTIONS = '#color_options';
  const HASH_REQUIRED = '#required';
  const HASH_DEFAULT_VALUE = '#default_value';
  const COLOR = 'color';
  const HASH_MARKUP = '#markup';

  /**
   * @return string[]
   */
  protected function getEditableConfigNames() {
    return [
      self::SPOTME_CONFIG_COLOR,
    ];
  }

  /**
   * @return string
   */
  public function getFormId() {
    return 'spot_me_custom_color_config';
  }

  /**
   * Implement function buildForm.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::SPOTME_CONFIG_COLOR);

    $form['tab'] = [
      '#type' => 'fieldgroup',
      '#title' => '<h2>Tab</h2>',
      '#suffix' => '<hr>',
    ];
    $form['tab']['tab_background_color'] = [
      '#type' => 'textfield',
      '#title' => 'Tab background color',
      self::HASH_DEFAULT_VALUE => $config->get('tab_background_color') ?? '#DCDCDC',
    ];
    $form['tab']['border_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Border color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('border_color') ?? '#ff0000',
      ],
    ];
    $form['tab']['tab_text_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Tab text color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('tab_text_color') ?? '#000000',
      ],
    ];

    $form['tab']['tab_text_hover_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Tab text hover color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('tab_text_hover_color') ?? '#000000',
      ],
    ];
    $form['tab']['tab_title_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Tab title color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('tab_title_color') ?? '#000000',
      ],
    ];

    $form['event_item'] = [
      '#type' => 'fieldgroup',
      '#title' => '<h2>Event item</h2>',
      '#suffix' => '<hr>',
    ];
    $form['event_item']['normal_text_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Normal text color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('normal_text_color') ?? '#000000',
      ],
    ];
    $form['event_item']['item_time_background_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Time background color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('item_time_background_color') ?? '#DCDCDC',
      ],
    ];
    $form['event_item']['item_time_text_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Time text color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('item_time_text_color') ?? '#000000',
      ],
    ];
    $form['event_item']['item_title_text_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Event name color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('item_title_text_color') ?? '#000000',
      ],
    ];
    $form['event_item']['item_title_text_hover_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Event name hover color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('item_title_text_hover_color') ?? '#ff0000',
      ],
    ];
    $form['event_item']['item_button_background_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button background color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('item_button_background_color') ?? '#ff0000',
      ],
    ];
    $form['event_item']['item_button_background_hover_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button background hover color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('item_button_background_hover_color') ?? '#ff0000',
      ],
    ];
    $form['event_item']['item_button_text_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button text color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('item_button_text_color') ?? '#ffffff',
      ],
    ];
    $form['event_item']['item_button_text_hover_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button text hover color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('item_button_text_hover_color') ?? '#ffffff',
      ],
    ];

    $form['register_event_popup'] = [
      '#type' => 'fieldgroup',
      '#title' => '<h2>Register event popup</h2>',
      '#suffix' => '<hr>',
    ];
    $form['register_event_popup']['message_text_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Message text color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('message_text_color') ?? '#000000',
      ],
    ];
    $form['register_event_popup']['popup_button_background_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button background color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('popup_button_background_color') ?? '#ffffff',
      ],
    ];
    $form['register_event_popup']['popup_button_background_hover_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button background hover color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('popup_button_background_hover_color') ?? '#ffffff',
      ],
    ];
    $form['register_event_popup']['popup_button_text_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button text color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('popup_button_text_color') ?? '#E73223',
      ],
    ];
    $form['register_event_popup']['popup_button_text_hover_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button text hover color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('popup_button_text_hover_color') ?? '#E73223',
      ],
    ];
    $form['register_event_popup']['popup_button_yes_background_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button yes background color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('popup_button_yes_background_color') ?? '#E73223',
      ],
    ];
    $form['register_event_popup']['popup_button_yes_background_hover_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button yes background hover color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('popup_button_yes_background_hover_color') ?? '#E73223',
      ],
    ];
    $form['register_event_popup']['popup_button_yes_text__color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button yes text color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('popup_button_yes_text__color') ?? '#ffffff',
      ],
    ];
    $form['register_event_popup']['popup_button_yes_text_hover_color'] = [
      '#type' => self::COLOR,
      '#title' => 'Button yes text hover color',
      self::HASH_COLOR_OPTIONS => [self::START_COLOR, self::END_COLOR],
      self::HASH_DEFAULT_VALUE => [
        self::COLOR => $config->get('popup_button_yes_text_hover_color') ?? '#ffffff',
      ],
    ];
    $form['#attached']['library'][] = 'spotme_events/spotme_events.backend';
    return parent::buildForm($form, $form_state);
  }

  /**
   * Implement function submitForm.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config(self::SPOTME_CONFIG_COLOR);
    $values = $form_state->getValues();
    $fields = [
      'tab_background_color',
      'border_color',
      'tab_text_color',
      'tab_text_hover_color',
      'tab_title_color',
      'item_time_background_color',
      'item_time_text_color',
      'item_title_text_color',
      'item_title_text_hover_color',
      'item_button_background_color',
      'item_button_background_hover_color',
      'item_button_text_color',
      'item_button_text_hover_color',
      'message_text_color',
      'popup_button_background_color',
      'popup_button_background_hover_color',
      'popup_button_text_color',
      'popup_button_text_hover_color',
      'popup_button_yes_background_color',
      'popup_button_yes_background_hover_color',
      'popup_button_yes_text__color',
      'popup_button_yes_text_hover_color',
    ];
    foreach ($fields as $field) {
      $config->set($field, $values[$field]);
    }
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
