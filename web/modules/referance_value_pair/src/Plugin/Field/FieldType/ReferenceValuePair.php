<?php

namespace Drupal\reference_value_pair\Plugin\Field\FieldType;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Random;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\TypedData\EntityDataDefinition;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\OptGroup;
use Drupal\Core\Render\Element;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataReferenceDefinition;
use Drupal\Core\TypedData\DataReferenceTargetDefinition;
use Drupal\Core\Validation\Plugin\Validation\Constraint\AllowedValuesConstraint;

/**
 * Plugin implementation of the 'reference_value_pair' field type.
 *
 * @FieldType(
 *   id = "reference_value_pair",
 *   label = @Translation("Reference value pair"),
 *   description = @Translation("Stores an entity reference and a value."),
 *   default_widget = "reference_value_autocomplete_widget",
 *   default_formatter = "reference_value_formatter",
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList"
 * )
 */
class ReferenceValuePair extends EntityReferenceItem {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'max_length' => 255,
      'is_ascii' => FALSE,
      'case_sensitive' => FALSE,
      'target_type' => \Drupal::moduleHandler()->moduleExists('node') ? 'node' : 'user',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'handler' => 'default',
      'handler_settings' => [],
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Text value'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);
    $settings = $field_definition->getSettings();
    $target_type_info = \Drupal::service('entity_type.manager')->getDefinition($settings['target_type']);

    $target_id_data_type = 'string';
    if ($target_type_info->entityClassImplements('\Drupal\Core\Entity\FieldableEntityInterface')) {
      $id_definition = \Drupal::service('entity_field.manager')->getBaseFieldDefinitions($settings['target_type'])[$target_type_info->getKey('id')];
      if ($id_definition->getType() === 'integer') {
        $target_id_data_type = 'integer';
      }
    }

    if ($target_id_data_type === 'integer') {
      $target_id_definition = DataReferenceTargetDefinition::create('integer')
        ->setLabel(new TranslatableMarkup('@label ID', ['@label' => $target_type_info->getLabel()]))
        ->setSetting('unsigned', TRUE);
    }
    else {
      $target_id_definition = DataReferenceTargetDefinition::create('string')
        ->setLabel(new TranslatableMarkup('@label ID', ['@label' => $target_type_info->getLabel()]));
    }
    $target_id_definition->setRequired(TRUE);
    $properties['target_id'] = $target_id_definition;

    $properties['entity'] = DataReferenceDefinition::create('entity')
      ->setLabel($target_type_info->getLabel())
      ->setDescription(new TranslatableMarkup('The referenced entity'))
      // The entity object is computed out of the entity ID.
      ->setComputed(TRUE)
      ->setReadOnly(FALSE)
      ->setTargetDefinition(EntityDataDefinition::create($settings['target_type']))
      // We can add a constraint for the target entity type. The list of
      // referenceable bundles is a field setting, so the corresponding
      // constraint is added dynamically in ::getConstraints().
      ->addConstraint('EntityType', $settings['target_type']);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $target_type = $field_definition->getSetting('target_type');
    $target_type_info = \Drupal::service('entity_type.manager')->getDefinition($target_type);
    $properties = static::propertyDefinitions($field_definition)['target_id'];
    if ($target_type_info->entityClassImplements('\Drupal\Core\Entity\FieldableEntityInterface') && $properties->getDataType() === 'integer') {
      $columns = [
        'target_id' => [
          'description' => 'The ID of the target entity.',
          'type' => 'int',
          'unsigned' => TRUE,
        ],
      ];
    }
    else {
      $columns = [
        'target_id' => [
          'description' => 'The ID of the target entity.',
          'type' => 'varchar_ascii',
          // If the target entities act as bundles for another entity type,
          // their IDs should not exceed the maximum length for bundles.
          'length' => $target_type_info->getBundleOf() ? EntityTypeInterface::BUNDLE_MAX_LENGTH : 255,
        ],
      ];
    }

    $columns['value'] = [
      'type' => $field_definition->getSetting('is_ascii') === TRUE ? 'varchar_ascii' : 'varchar',
      'length' => (int) $field_definition->getSetting('max_length'),
      'binary' => $field_definition->getSetting('case_sensitive'),
    ];

    $schema = [
      'columns' => $columns,
      'indexes' => [
        'target_id' => ['target_id'],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();
    // Remove the 'AllowedValuesConstraint' validation constraint because entity
    // reference fields already use the 'ValidReference' constraint.
    foreach ($constraints as $key => $constraint) {
      if ($constraint instanceof AllowedValuesConstraint) {
        unset($constraints[$key]);
      }
    }

    if ($max_length = $this->getSetting('max_length')) {
      $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
      $constraints[] = $constraint_manager->create('ComplexData', [
        'value' => [
          'Length' => [
            'max' => $max_length,
            'maxMessage' => t('%name: may not be longer than @max characters.', [
              '%name' => $this->getFieldDefinition()->getLabel(),
              '@max' => $max_length,
            ]),
          ],
        ],
      ]);
    }

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values['value'] = $random->word(mt_rand(1, $field_definition->getSetting('max_length')));
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $elements = [];

    $elements['max_length'] = [
      '#type' => 'number',
      '#title' => t('Maximum length'),
      '#default_value' => $this->getSetting('max_length'),
      '#required' => TRUE,
      '#description' => t('The maximum length of the field in characters.'),
      '#min' => 1,
      '#disabled' => $has_data,
    ];

    $elements['target_type'] = [
      '#type' => 'select',
      '#title' => t('Type of item to reference'),
      '#options' => \Drupal::service('entity_type.repository')->getEntityTypeLabels(TRUE),
      '#default_value' => $this->getSetting('target_type'),
      '#required' => TRUE,
      '#disabled' => $has_data,
      '#size' => 1,
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    if (!$this->isEntityEmpty()) {
      return FALSE;
    }
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * Check if the entity is set. Copied from EntityReferenceItem::isEmpty().
   *
   * @return bool
   */
  public function isEntityEmpty() {
    // Avoid loading the entity by first checking the 'target_id'.
    if ($this->target_id !== NULL) {
      return FALSE;
    }
    if ($this->entity && $this->entity instanceof EntityInterface) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave() {
    if ($this->hasNewEntity()) {
      // Save the entity if it has not already been saved by some other code.
      if ($this->entity->isNew()) {
        $this->entity->save();
      }
      // Make sure the parent knows we are updating this property so it can
      // react properly.
      $this->target_id = $this->entity->id();
    }
    if (!$this->isEntityEmpty() && $this->target_id === NULL) {
      $this->target_id = $this->entity->id();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $field = $form_state->getFormObject()->getEntity();

    // Get all selection plugins for this entity type.
    $selection_plugins = \Drupal::service('plugin.manager.entity_reference_selection')->getSelectionGroups($this->getSetting('target_type'));
    $handlers_options = [];
    foreach (array_keys($selection_plugins) as $selection_group_id) {
      // We only display base plugins (e.g. 'default', 'views', ...) and not
      // entity type specific plugins (e.g. 'default:node', 'default:user',
      // ...).
      if (array_key_exists($selection_group_id, $selection_plugins[$selection_group_id])) {
        $handlers_options[$selection_group_id] = Html::escape($selection_plugins[$selection_group_id][$selection_group_id]['label']);
      }
      elseif (array_key_exists($selection_group_id . ':' . $this->getSetting('target_type'), $selection_plugins[$selection_group_id])) {
        $selection_group_plugin = $selection_group_id . ':' . $this->getSetting('target_type');
        $handlers_options[$selection_group_plugin] = Html::escape($selection_plugins[$selection_group_id][$selection_group_plugin]['base_plugin_label']);
      }
    }

    $form = [
      '#type' => 'container',
      '#process' => [[get_class($this), 'fieldSettingsAjaxProcess']],
      '#element_validate' => [[get_class($this), 'fieldSettingsFormValidate']],

    ];
    $form['handler'] = [
      '#type' => 'details',
      '#title' => $this->t('Reference type'),
      '#open' => TRUE,
      '#tree' => TRUE,
      '#process' => [[get_class($this), 'formProcessMergeParent']],
    ];

    $form['handler']['handler'] = [
      '#type' => 'select',
      '#title' => $this->t('Reference method'),
      '#options' => $handlers_options,
      '#default_value' => $field->getSetting('handler'),
      '#required' => TRUE,
      '#ajax' => TRUE,
      '#limit_validation_errors' => [],
    ];
    $form['handler']['handler_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Change handler'),
      '#limit_validation_errors' => [],
      '#attributes' => [
        'class' => ['js-hide'],
      ],
      '#submit' => [[get_class($this), 'settingsAjaxSubmit']],
    ];

    $form['handler']['handler_settings'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['entity_reference-settings']],
    ];

    $handler = \Drupal::service('plugin.manager.entity_reference_selection')->getSelectionHandler($field);
    $form['handler']['handler_settings'] += $handler->buildConfigurationForm([], $form_state);

    return $form;
  }

  /**
   * Form element validation handler; Invokes selection plugin's validation.
   *
   * @param array $form
   *   The form where the settings form is being included in.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state of the (entire) configuration form.
   */
  public static function fieldSettingsFormValidate(array $form, FormStateInterface $form_state) {
    $field = $form_state->getFormObject()->getEntity();
    $handler = \Drupal::service('plugin.manager.entity_reference_selection')->getSelectionHandler($field);
    $handler->validateConfigurationForm($form, $form_state);
  }

  /**
   * Determines whether the item holds an unsaved entity.
   *
   * This is notably used for "autocreate" widgets, and more generally to
   * support referencing freshly created entities (they will get saved
   * automatically as the hosting entity gets saved).
   *
   * @return bool
   *   TRUE if the item holds an unsaved entity.
   */
  public function hasNewEntity() {
    return !$this->isEntityEmpty() && $this->target_id === NULL && $this->entity->isNew();
  }

  /**
   * {@inheritdoc}
   */
  public static function calculateDependencies(FieldDefinitionInterface $field_definition) {
    $dependencies = parent::calculateDependencies($field_definition);
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // We are assuming that we want to use the `entity_type.manager` service since no method was called here directly. Please confirm this is the case. See https://www.drupal.org/node/2549139 for more information.
    $manager = \Drupal::service('entity_type.manager');
    $target_entity_type = $manager->getDefinition($field_definition->getFieldStorageDefinition()->getSetting('target_type'));

    // Depend on default values entity types configurations.
    if ($default_value = $field_definition->getDefaultValueLiteral()) {
      foreach ($default_value as $value) {
        if (is_array($value) && isset($value['target_uuid'])) {
          $entity = \Drupal::service('entity.repository')->loadEntityByUuid($target_entity_type->id(), $value['target_uuid']);
          // If the entity does not exist do not create the dependency.
          // @see \Drupal\Core\Field\EntityReferenceFieldItemList::processDefaultValue()
          if ($entity) {
            $dependencies[$target_entity_type->getConfigDependencyKey()][] = $entity->getConfigDependencyName();
          }
        }
      }
    }

    // Depend on target bundle configurations.
    $handler = $field_definition->getSetting('handler_settings');
    if (!empty($handler['target_bundles'])) {
      if ($bundle_entity_type_id = $target_entity_type->getBundleEntityType()) {
        if ($storage = $manager->getStorage($bundle_entity_type_id)) {
          foreach ($storage->loadMultiple($handler['target_bundles']) as $bundle) {
            $dependencies[$bundle->getConfigDependencyKey()][] = $bundle->getConfigDependencyName();
          }
        }
      }
    }

    return $dependencies;
  }

  /**
   * {@inheritdoc}
   */
  public static function calculateStorageDependencies(FieldStorageDefinitionInterface $field_definition) {
    $dependencies = parent::calculateStorageDependencies($field_definition);
    $target_entity_type = \Drupal::service('entity_type.manager')->getDefinition($field_definition->getSetting('target_type'));
    $dependencies['module'][] = $target_entity_type->getProvider();
    return $dependencies;
  }

  /**
   * {@inheritdoc}
   */
  public static function onDependencyRemoval(FieldDefinitionInterface $field_definition, array $dependencies) {
    $changed = parent::onDependencyRemoval($field_definition, $dependencies);
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // We are assuming that we want to use the `entity_type.manager` service since no method was called here directly. Please confirm this is the case. See https://www.drupal.org/node/2549139 for more information.
    $entity_manager = \Drupal::service('entity_type.manager');
    $target_entity_type = $entity_manager->getDefinition($field_definition->getFieldStorageDefinition()->getSetting('target_type'));

    // Try to update the default value config dependency, if possible.
    if ($default_value = $field_definition->getDefaultValueLiteral()) {
      foreach ($default_value as $key => $value) {
        if (is_array($value) && isset($value['target_uuid'])) {
          $entity = \Drupal::service('entity.repository')->loadEntityByUuid($target_entity_type->id(), $value['target_uuid']);
          // @see \Drupal\Core\Field\EntityReferenceFieldItemList::processDefaultValue()
          if ($entity && isset($dependencies[$entity->getConfigDependencyKey()][$entity->getConfigDependencyName()])) {
            unset($default_value[$key]);
            $changed = TRUE;
          }
        }
      }
      if ($changed) {
        $field_definition->setDefaultValue($default_value);
      }
    }

    // Update the 'target_bundles' handler setting if a bundle config dependency
    // has been removed.
    $bundles_changed = FALSE;
    $handler_settings = $field_definition->getSetting('handler_settings');
    if (!empty($handler_settings['target_bundles'])) {
      if ($bundle_entity_type_id = $target_entity_type->getBundleEntityType()) {
        if ($storage = $entity_manager->getStorage($bundle_entity_type_id)) {
          foreach ($storage->loadMultiple($handler_settings['target_bundles']) as $bundle) {
            if (isset($dependencies[$bundle->getConfigDependencyKey()][$bundle->getConfigDependencyName()])) {
              unset($handler_settings['target_bundles'][$bundle->id()]);
              $bundles_changed = TRUE;

              // In case we deleted the only target bundle allowed by the field
              // we have to log a warning message because the field will not
              // function correctly anymore.
              if ($handler_settings['target_bundles'] === []) {
                \Drupal::logger('entity_reference')->critical('The %target_bundle bundle (entity type: %target_entity_type) was deleted. As a result, the %field_name entity reference field (entity_type: %entity_type, bundle: %bundle) no longer has any valid bundle it can reference. The field is not working correctly anymore and has to be adjusted.', [
                  '%target_bundle' => $bundle->label(),
                  '%target_entity_type' => $bundle->getEntityType()->getBundleOf(),
                  '%field_name' => $field_definition->getName(),
                  '%entity_type' => $field_definition->getTargetEntityTypeId(),
                  '%bundle' => $field_definition->getTargetBundle(),
                ]);
              }
            }
          }
        }
      }
    }
    if ($bundles_changed) {
      $field_definition->setSetting('handler_settings', $handler_settings);
    }
    $changed |= $bundles_changed;

    return $changed;
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    return $this->getSettableValues($account);
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return $this->getSettableOptions($account);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    // Flatten options first, because "settable options" may contain group
    // arrays.
    $flatten_options = OptGroup::flattenOptions($this->getSettableOptions($account));
    return array_keys($flatten_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    $field_definition = $this->getFieldDefinition();
    if (!$options = \Drupal::service('plugin.manager.entity_reference_selection')->getSelectionHandler($field_definition, $this->getEntity())->getReferenceableEntities()) {
      return [];
    }

    // Rebuild the array by changing the bundle key into the bundle label.
    $target_type = $field_definition->getSetting('target_type');
    $bundles = \Drupal::service('entity_type.bundle.info')->getBundleInfo($target_type);

    $return = [];
    foreach ($options as $bundle => $entity_ids) {
      // The label does not need sanitizing since it is used as an optgroup
      // which is only supported by select elements and auto-escaped.
      $bundle_label = (string) $bundles[$bundle]['label'];
      $return[$bundle_label] = $entity_ids;
    }

    return count($return) == 1 ? reset($return) : $return;
  }

  /**
   * Render API callback: Processes the field settings form and allows access to
   * the form state.
   *
   * @see static::fieldSettingsForm()
   */
  public static function fieldSettingsAjaxProcess($form, FormStateInterface $form_state) {
    static::fieldSettingsAjaxProcessElement($form, $form);
    return $form;
  }

  /**
   * Adds entity_reference specific properties to AJAX form elements from the
   * field settings form.
   *
   * @see static::fieldSettingsAjaxProcess()
   */
  public static function fieldSettingsAjaxProcessElement(&$element, $main_form) {
    if (!empty($element['#ajax'])) {
      $element['#ajax'] = [
        'callback' => [get_called_class(), 'settingsAjax'],
        'wrapper' => $main_form['#id'],
        'element' => $main_form['#array_parents'],
      ];
    }

    foreach (Element::children($element) as $key) {
      static::fieldSettingsAjaxProcessElement($element[$key], $main_form);
    }
  }

  /**
   * Render API callback: Moves entity_reference specific Form API elements
   * (i.e. 'handler_settings') up a level for easier processing by the
   * validation and submission handlers.
   *
   * @see _entity_reference_field_settings_process()
   */
  public static function formProcessMergeParent($element) {
    $parents = $element['#parents'];
    array_pop($parents);
    $element['#parents'] = $parents;
    return $element;
  }

  /**
   * Ajax callback for the handler settings form.
   *
   * @see static::fieldSettingsForm()
   */
  public static function settingsAjax($form, FormStateInterface $form_state) {
    return NestedArray::getValue($form, $form_state->getTriggeringElement()['#ajax']['element']);
  }

  /**
   * Submit handler for the non-JS case.
   *
   * @see static::fieldSettingsForm()
   */
  public static function settingsAjaxSubmit($form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions() {
    return [];
  }

}
