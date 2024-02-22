<?php

namespace Drupal\spotme_events\Plugin\views\field;

use Drupal\node\Entity\Node;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\Views;

/**
 * Field handler to provide a register event date.
 *
 * @ViewsField("spotme_custom_event_magic_link")
 */
class EventMagicLinkCustom extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $value = '';
    if (isset($values->_relationship_entities['reverse__user__field_registered_event'])
      && $values->_relationship_entities['reverse__user__field_registered_event']) {
      $user_id = $values->_relationship_entities['reverse__user__field_registered_event']->uid->value;
      $event_id = $values->_entity->nid->value;
      $sql = \Drupal::database()->select('user__field_event_magic_link', 'feml');
      $sql->fields('feml', ['field_event_magic_link_value']);
      $sql->condition('feml.entity_id', $user_id);
      $sql->condition('feml.field_event_magic_link_target_id', $event_id);
      $value = $sql->execute()->fetchField();
    }
    return $this->sanitizeValue($value);
  }

}
