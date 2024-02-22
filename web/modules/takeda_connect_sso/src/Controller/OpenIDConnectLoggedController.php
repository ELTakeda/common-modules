<?php

namespace Drupal\openid_connect\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\openid_connect\OpenIDConnect;
use Drupal\openid_connect\Plugin\OpenIDConnectClientManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Url;
use GuzzleHttp\ClientInterface;

class OpenIDConnectLoggedController extends ControllerBase implements AccessInterface {

  protected $openIDConnect;
  protected $currentUser;
  protected $sessionManager;
  protected $pluginManager;
  protected $httpClient;

  public function __construct(
    OpenIDConnect $openid_connect,
    AccountProxyInterface $currentUser,
    SessionManagerInterface $sessionManager,
    OpenIDConnectClientManager $pluginManager,
    ClientInterface $http_client)
  {
    $this->openIDConnect = $openid_connect;
    $this->currentUser = $currentUser;
    $this->sessionManager = $sessionManager;
    $this->pluginManager = $pluginManager;
    $this->httpClient = $http_client;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('openid_connect.openid_connect'),
      $container->get('current_user'),
      $container->get('session_manager'),
      $container->get('plugin.manager.openid_connect_client'),
      $container->get('http_client')
    );
  }

  public function is_user_logged() {

    $isLogged = $this->currentUser->isAuthenticated();

    $return = array();
    $return['loggedIn'] = $isLogged;
    $return['loggedInThroughOkta'] = false;

    if ($isLogged) {
          $return['email'] = $this->currentUser->getEmail();
          if (array_key_exists("openid_connect", $_SESSION)) {
                if (array_key_exists("tokens", $_SESSION["openid_connect"])) {

		    $return['loggedInThroughOkta'] = true;

		    $return['keysAreOK'] = $this->update_tokens_if_needed();

		    $id_token = $_SESSION["openid_connect"]["tokens"]["id_token"];
		    $return['idToken'] = $id_token;

		    $access_token = $_SESSION["openid_connect"]["tokens"]["access_token"];
		    $return['accessToken'] = $access_token;
                }
          }
    }

    if ((!array_key_exists('accessToken', $return) && $isLogged == FALSE) ||
        (array_key_exists('keysAreOK', $return)) && $return['keysAreOK'] == false) {
        $configuration = $this->config('openid_connect.settings.okta')->get('settings');
        $client = $this->pluginManager->createInstance('okta', $configuration);
        $scope = 'openid email offline_access';

        $return['url'] = $client->getAuthEndpoint($scope, FALSE);
    }

    return new JsonResponse($return);
  }

  protected function update_tokens_if_needed() {
        $configuration = $this->config('openid_connect.settings.okta')->get('settings');
        $client = $this->pluginManager->createInstance('okta', $configuration);

	$id_token = $_SESSION["openid_connect"]["tokens"]["id_token"];
	$claims = $client->decodeIdToken($id_token);

        $current_time = time();

        // Max time for renewal : 15 minutes
        $allowed_diff = 15 * 60;

        if (is_array($claims) && array_key_exists("exp", $claims)) {

            // Renew keys if needed
            if ($claims["exp"] < $current_time + $allowed_diff) { return $this->update_tokens(); } else { return true; }
        } 

        return false;
  }

  protected function update_tokens() {

	$refresh_token = $_SESSION["openid_connect"]["tokens"]["refresh_token"];

        $configuration = $this->config('openid_connect.settings.okta')->get('settings');
        $client = $this->pluginManager->createInstance('okta', $configuration);
        $redirect_uri = $client->getRedirectUrl()->toString();
        $endpoints = $client->getEndpoints();
        $scope = 'openid email offline_access';

        $request_options = [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'redirect_uri' => $redirect_uri,
                'scope' => $scope,
                'refresh_token' => $refresh_token,
                'client_id' => $configuration['client_id'],
                'client_secret' => $configuration['client_secret']
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-type' => 'application/x-www-form-urlencoded'
            ]
        ];

        $http_client = $this->httpClient;

        try {
            $response = $http_client->post($endpoints['token'], $request_options);
            $response_data = json_decode((string) $response->getBody(), TRUE);

            // If no data received - error
            if (!array_key_exists('access_token', $response_data) ||
                !array_key_exists('id_token', $response_data) ||
                !array_key_exists('refresh_token', $response_data)) {
                return false;
            }

            $_SESSION["openid_connect"]["tokens"]["refresh_token"] = $response_data['refresh_token'];
            $_SESSION["openid_connect"]["tokens"]["id_token"] = $response_data['id_token'];
            $_SESSION["openid_connect"]["tokens"]["access_token"] = $response_data['access_token'];

            return true;
        }
        catch (\Exception $e) {
            // Error with key exchange, return false
            return false;
        }
  }
}