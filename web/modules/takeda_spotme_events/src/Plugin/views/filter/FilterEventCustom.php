<?php

namespace Drupal\spotme_events\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\spotme_events\Service\HelperService;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;

/**
 * Filter by records that has charge person.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("spot_me_filter_event_custom")
 */
class FilterEventCustom extends InOperator {

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->valueOptions = [
      'upcoming_event' => 'Up coming Events',
      'current_event' => 'Current Events',
      'new_arrival' => 'New arrival Events',
      'past_event' => 'Past Events',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function canExpose() {
    return FALSE;
  }

  /**
   * Filter job content by posting place.
   */
  public function query() {
    if (!$this->value) {
      return;
    }
    $table = $this->ensureMyTable();
    $config = \Drupal::config('spotme.mulesoft_api_config');
    $upcoming_event_before_day = $config->get('upcoming_event_before_day') ?? '30';
    if (isset(\Drupal::config('system.date')->get('timezone')['default'])) {
      date_default_timezone_set(\Drupal::config('system.date')->get('timezone')['default']);
      $now = date('Y/m/d H:i');
    }
    if (in_array('current_event', $this->value)) {
       $this->query->addWhereExpression($this->options['group']," $table.nid IN (
      SELECT node__field_start_at_custom.entity_id
      FROM
        node__field_start_at AS node__field_start_at_custom
      JOIN node_field_data AS n1 ON n1.nid = node__field_start_at_custom.entity_id
      JOIN node__field_duration_in_minites AS node__field_duration_in_minites_custom
        ON node__field_duration_in_minites_custom.entity_id = node__field_duration_in_minites_custom.entity_id
        AND node__field_duration_in_minites_custom.entity_id = n1.nid
      WHERE
      DATE_FORMAT(node__field_start_at_custom.field_start_at_value, '%Y/%m/%d %H:%i') < :now and
      DATE_FORMAT(date_add(DATE_FORMAT(node__field_start_at_custom.field_start_at_value, '%Y/%m/%d %H:%i'),interval node__field_duration_in_minites_custom.field_duration_in_minites_value minute), '%Y/%m/%d %H:%i') > :now
      )", [':now' => $now]);
    }
    elseif (in_array('past_event', $this->value)) {
      $this->query->addWhereExpression($this->options['group']," $table.nid IN (
      SELECT node__field_start_at_custom.entity_id
      FROM
        node__field_start_at AS node__field_start_at_custom
      JOIN node_field_data AS n1 ON n1.nid = node__field_start_at_custom.entity_id
      JOIN node__field_duration_in_minites AS node__field_duration_in_minites_custom
        ON node__field_duration_in_minites_custom.entity_id = node__field_duration_in_minites_custom.entity_id
        AND node__field_duration_in_minites_custom.entity_id = n1.nid
      WHERE
       DATE_FORMAT(date_add(DATE_FORMAT(node__field_start_at_custom.field_start_at_value, '%Y/%m/%d %H:%i'),interval node__field_duration_in_minites_custom.field_duration_in_minites_value minute),'%Y/%m/%d %H:%i') < :now
      )", [':now' => $now]);
    }
    elseif (in_array('upcoming_event', $this->value)) {
      $this->query->addWhereExpression($this->options['group']," $table.nid IN (
      SELECT node__field_start_at_custom.entity_id
      FROM
        node__field_start_at AS node__field_start_at_custom
      JOIN node_field_data AS n1 ON n1.nid = node__field_start_at_custom.entity_id
      JOIN node__field_duration_in_minites AS node__field_duration_in_minites_custom
        ON node__field_duration_in_minites_custom.entity_id = node__field_duration_in_minites_custom.entity_id
        AND  node__field_duration_in_minites_custom.entity_id = n1.nid
      WHERE
      DATE_FORMAT(node__field_start_at_custom.field_start_at_value, '%Y/%m/%d %H:%i') > :now AND
      DATE_FORMAT(date_add(DATE_FORMAT(node__field_start_at_custom.field_start_at_value, '%Y/%m/%d %H:%i'),interval -" . $upcoming_event_before_day . " day),'%Y/%m/%d %H:%i') <= :now
      )", [':now' => $now]);
    }
    else {
      $this->query->addWhereExpression($this->options['group']," $table.nid IN (
      SELECT node__field_start_at_custom.entity_id
      FROM
        node__field_start_at AS node__field_start_at_custom
      JOIN node_field_data AS n1 ON n1.nid = node__field_start_at_custom.entity_id
      JOIN node__field_duration_in_minites AS node__field_duration_in_minites_custom
        ON node__field_duration_in_minites_custom.entity_id = node__field_duration_in_minites_custom.entity_id
        AND node__field_duration_in_minites_custom.entity_id = n1.nid
      WHERE
      DATE_FORMAT(node__field_start_at_custom.field_start_at_value, '%Y/%m/%d %H:%i') > :now
      )", [':now' => $now]);
    }
  }

}
