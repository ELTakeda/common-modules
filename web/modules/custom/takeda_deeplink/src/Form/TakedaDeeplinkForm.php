<?php

namespace Drupal\takeda_deeplink\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * My Module configuration form.
 */
class TakedaDeeplinkForm extends ConfigFormBase
{

    const DEEPLINK_TABLE = 'takeda_deeplink';
    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return ['takeda_deeplink.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'takeda_deeplink_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Get a list of content types.
        $content_types = node_type_get_names();
        $table = self::DEEPLINK_TABLE;
        $active_deeplink = $this->getDbData($table);
        $active_nids = [];
        $active_content_types = [];
        
        foreach ($active_deeplink as $key => $value) {
            $active_nids[$value->nid] = $value->nid;
            $node = \Drupal\node\Entity\Node::load($value->nid);
            if ($node) {
                $active_content_types[$node->getType()] = $node->getType();
            }
            $active_content_types[$value->nid] = $value->nid;
            # code...
        }

        $config = $this->config('takeda_deeplink.settings');

        $form['client_id'] = array(
            '#type' => 'textfield',
            '#title' => $this
                ->t('Client ID'),
            '#default_value' => (!empty($config->get('client_id')) ? $config->get('client_id') : ''),
            '#size' => 60,
            '#maxlength' => 128,
            '#required' => true,
        );

        $form['client_secret'] = array(
            '#type' => 'textfield',
            '#title' => $this
                ->t('Client Secret'),
            '#default_value' => (!empty($config->get('client_secret')) ? $config->get('client_secret') : ''),
            '#size' => 60,
            '#maxlength' => 128,
            '#required' => true,
        );

        $form['redirect_url'] = array(
            '#type' => 'textfield',
            '#title' => $this
                ->t('Login Redirect URL'),
            '#default_value' => (!empty($config->get('redirect_url')) ? $config->get('redirect_url') : ''),
            '#required' => true,
        );

        $form['request_url'] = array(
            '#type' => 'textfield',
            '#title' => $this
                ->t('Request URL'),
            '#default_value' => (!empty($config->get('request_url')) ? $config->get('request_url') : ''),
            '#required' => true,
        );

        // First select box for content types.
        $form['content_types'] = array(
            '#type' => 'select',
            '#ajax_data' => 0,
            '#title' => t('Select Content Types'),
            '#ajax' => [
                'callback' => [$this, 'updateNodeSelect'],
                'wrapper' => 'node-select-wrapper',
                'event' => 'change',
                'method' => 'replace',
                'progress' => [
                    'type' => 'throbber',
                    'message' => $this->t('Verifying entry...'),
                ],
                'disable-refocus' => true,
            ],
            '#multiple' => true,
            '#options' => $content_types,
            '#default_value' => $active_content_types,
        );

        $selected_content_types = $form['content_types']['#default_value'];
        $node_options = ($selected_content_types ? $this->getNodeOptions($selected_content_types) : []);

        // Second select box for nodes.
        $form['nodes'] = array(
            '#type' => 'select',
            '#title' => t('Select Nodes'),
            '#multiple' => true,
            '#validated' => 'true',
            '#display' => 'visible',
            '#prefix' => '<div id="node-select-wrapper">',
            '#suffix' => '</div>',

        );

        if ($config->get('selected_node')) {
            $form['nodes']['#options'] = $node_options;
            $form['nodes']['#default_value'] = $active_nids;

        }

        // Add submit button.
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => 'Save Settings',
            '#button_type' => 'primary',
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Save the configuration values.
        $connection = \Drupal::database();
        $table = self::DEEPLINK_TABLE;
        $nodes = $form_state->getValue('nodes');

        $config = $this->config('takeda_deeplink.settings');
        $config->set('client_id', $form_state->getValue('client_id'));
        $config->set('client_secret', $form_state->getValue('client_secret'));
        $config->set('redirect_url', $form_state->getValue('redirect_url'));
        $config->set('request_url', $form_state->getValue('request_url'));
        $config->set('selected_content_type', $form_state->getValue('content_types'));
        $config->set('selected_node', $form_state->getValue('nodes'));
        $nids = \Drupal::entityQuery('node')->accessCheck(false)->execute();
        $fields = [];

        foreach ($nids as $key => $node) {
            # code...
            $fields['nid'] = (integer) $node;
            $fields['is_deeplink_auth_active'] = (in_array($node, $nodes) ? 1 : 0);
            $upsert = $connection->upsert($table)
                ->fields($fields)
                ->key('nid');
            $upsert->values($fields);
            $upsert->execute();
        }

        try {
            $config->save();

        } catch (\Throwable $th) {
            dump($th);
            die;
        }
    }

    /**
     * Ajax callback to update the second select box based on selected content types.
     */
    public function updateNodeSelect(array &$form, FormStateInterface $form_state)
    {
        $table = self::DEEPLINK_TABLE;
        $config = $this->config('takeda_deeplink.settings');
        $selected_content_types = $form_state->getValue('content_types');

        $node_options = $this->getNodeOptions($selected_content_types);
        $selected_nodes = $form_state->getValue('nodes');

        $active_deeplink = $this->getDbData($table);
        $active_nids = [];
        foreach ($active_deeplink as $key => $value) {
            $active_nids[$value->nid] = $value->nid;
        }
        // Log the selected nodes for debugging.
        \Drupal::logger('takeda_deeplink')->notice('Selected Nodes: @nodes', ['@nodes' => print_r($selected_nodes, true)]);

        // Filter out any selected nodes that are not in the current options.
        $selected_nodes = array_intersect($selected_nodes, array_keys($node_options));
        // Log the filtered selected nodes for debugging.
        \Drupal::logger('takeda_deeplink')->notice('Filtered Selected Nodes: @nodes', ['@nodes' => print_r($selected_nodes, true)]);

        if ($node_options) {
            $form['nodes']['#options'] = $node_options;
            $form['nodes']['#default_value'] = $active_nids;

        }
        return $form['nodes'];
    }
    /**
     * Helper function to get node options based on selected content types.
     */
    private function getNodeOptions(array $selected_content_types)
    {
        $node_options = [];

        foreach ($selected_content_types as $content_type) {
            $query = \Drupal::entityQuery('node')
                ->condition('type', $content_type)
                ->accessCheck(false)
                ->condition('status', 1);
            $nids = $query->execute();
            $nodes = Node::loadMultiple($nids);

            foreach ($nodes as $node) {
                $node_options[$node->id()] = $node->label();
            }
        }

        return $node_options;
    }

    public function getDbData($table)
    {
        $connection = \Drupal::database();
        $select = $connection->select($table, 't')
            ->condition('is_deeplink_auth_active', 1, '=')
            ->fields('t', ['nid', 'is_deeplink_auth_active']);
        $result = $select->execute();
        $result = $result->fetchAll();
        return $result;
    }
}
