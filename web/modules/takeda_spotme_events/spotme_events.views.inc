<?php

/**
 * @file
 * Contains view API hooks.
 */

/**
 * Implements hook_views_data().
 */
function spotme_events_views_data() {
  $data = [];

  $data['node_field_data']['spot_me_filter_event_custom'] = [
    'title' => t('Filter event custom'),
    'group' => 'Spotme Custom',
    'filter' => [
      'id' => 'spot_me_filter_event_custom',
    ],
  ];

  $data['node_field_data']['spotme_custom_register_event_date'] = [
    'title' => t('Register event date'),
    'field' => [
      'id' => 'spotme_custom_register_event_date',
    ],
  ];
  $data['node_field_data']['spotme_custom_event_magic_link'] = [
    'title' => t('Event magic link'),
    'field' => [
      'id' => 'spotme_custom_event_magic_link',
    ],
  ];

  return $data;
}
