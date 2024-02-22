<?php

namespace Drupal\openid_connect\Plugin\OpenIDConnectClient;

use Drupal\Core\Form\FormStateInterface;
use Drupal\openid_connect\Plugin\OpenIDConnectClientBase;

/**
 * Okta OpenID Connect client.
 *
 * Implements OpenID Connect Client plugin for Okta.
 *
 * @OpenIDConnectClient(
 *   id = "okta",
 *   label = @Translation("Okta")
 * )
 */
class OpenIDConnectOktaClient extends OpenIDConnectClientBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'okta_domain' => 'takedaext.oktapreview.com',
      'only_hcp' => true
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['okta_domain'] = [
      '#title' => $this->t('Okta domain'),
      '#type' => 'textfield',
      '#default_value' => $this->configuration['okta_domain'],
    ];

    $form['only_hcp'] = [
      '#title' => $this->t('Only verified HCP users may login?'),
      '#type' => 'checkbox',
      '#default_value' => $this->configuration['only_hcp'],
    ];

    $form['test_button'] = array(
       '#type' => 'submit',
       '#value' => $this->t('Test Okta Connection'),
       '#submit' => array('::test_okta'),
       '#validate' => array('::save_okta'),
       '#attributes' => array(
           'style' => 'width: 100%; margin: 0;'
       )
    );

    $form['test_result'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#disabled' => TRUE,
      '#value' => 'Not tested yet',
      '#attributes' => [
         'id' => ['test_okta'],
         'style' => 'text-align: center;'
       ],
    ];

    if (isset($_SESSION['test_only'])) {
        if ($_SESSION['test_only'] && isset($_SESSION['openid_connect']['tokens'])) {
            $form['test_result']['#value'] = 'Test passed!';
            $form['test_result']['#attributes']['style'] = 'text-align: center; background-color: #6dd16d; color: black;';

            unset($_SESSION['openid_connect']['tokens']);
        }

        unset($_SESSION['test_only']);
    }


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndpoints() {
    // From https://developer.okta.com/docs/reference/api/oidc and
    // https://${yourOktaDomain}/.well-known/openid-configuration
    return [
      'authorization' => 'https://' . $this->configuration['okta_domain'] . '/oauth2/default/v1/authorize',
      'token' => 'https://' . $this->configuration['okta_domain'] . '/oauth2/default/v1/token',
      'userinfo' => 'https://' . $this->configuration['okta_domain'] . '/oauth2/default/v1/userinfo',
      'logout' => 'https://' . $this->configuration['okta_domain'] . '/oauth2/default/v1/logout',
    ];
  }

}
