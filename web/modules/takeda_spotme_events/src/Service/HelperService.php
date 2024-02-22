<?php

namespace Drupal\spotme_events\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use GuzzleHttp\Exception\RequestException;

/**
 * Class HelperService.
 */
class HelperService implements HelperServiceInterface {

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $currentUser;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc }
   */
  protected $configFactory;

  /**
   * {@inheritdoc }
   */
  protected $database;

  /**
   * {@inheritdoc }
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager,
                              AccountInterface $current_user,
                              ConfigFactoryInterface $configFactory,
                              Connection $database,
                              LoggerChannelFactoryInterface $loggerFactory) {
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $current_user;
    $this->configFactory = $configFactory;
    $this->database = $database;
    $this->logger = $loggerFactory;
  }

  /**
   * {@inheritdoc }
   */
  public function postEvent($data) {

  }

  /**
   * {@inheritdoc }
   */
  public function getLinkEvent($data) {
    if (isset($data['user'])) {
      $user = $data['user'];
    }
    else {
      $user = User::load(\Drupal::currentUser()->id());
    }

    // Mulesoft API config.
    $muleSoftConfigApi = $this->getMuleSoftApiConfig();

    $user_takeda_id = $user->field_takeda_id->value;
    $apiUrl = $muleSoftConfigApi['url_endpoint_magic_link'] . "/webinar/attendee?takedaId=" . $user_takeda_id
      . "&webinarId=" . $data['webinarId'] . "&orgId=" . $muleSoftConfigApi['org_id'];

    // Transaction id.
    $transactionId = $this->generateTransactionApiId('link_event', $user_takeda_id);

    // Call API get Data.
    $client = \Drupal::httpClient();
    $response = [];
    try {
      $request = $client->get($apiUrl, [
        "headers" => [
          "client_id" => $muleSoftConfigApi['client_id_magic_link'],
          "client_secret" => $muleSoftConfigApi['client_secret_magic_link'],
          'x-correlation-id' => $transactionId
        ],
      ]);
      $response = json_decode($request->getBody(), true);

      $this->logger->get('api_log')->info(
        '<pre><code>' . print_r([
          'x-correlation-id' => $transactionId,
          'url' => $apiUrl,
          'response' =>$response,
        ], TRUE) . '<pre><code>'
      );
      return $response;

    } catch (RequestException $e) {
      $this->logger->get('api_log')->error(
        '<pre><code>' . print_r([
          'x-correlation-id' => $transactionId,
          'url' => $apiUrl,
          'message' => str_replace("\n", "", $e->getMessage())
        ], TRUE) . '<pre><code>'
      );
    }
    return $response;

  }

  /**
   * {@inheritdoc }
   */
  public function registerEvent($data_event) {
    if (empty($data_event)) {
      return [];
    }

    $event_nid = $data_event['data_crm']['registerAttendees']['nid'];
    unset($data_event['data_crm']['registerAttendees']['nid']);
    $send_mail = FALSE;
    if (isset($data_event['send_mail'])) {
      unset($data_event['send_mail']);
      $send_mail = TRUE;
    }

    // Mulesoft API config.
    $muleSoftConfigApi = $this->getMuleSoftApiConfig();

    $apiUrl = $muleSoftConfigApi['url_endpoint_magic_link'] . "/webinar/attendee";

    // Transaction id.
    $transactionId = $this->generateTransactionApiId('register_event', $data_event['data_crm']['registerAttendees']['webinarId']);

    // Call API get Data.
    $client = \Drupal::httpClient();
    $response = [];
    try {
      $request = $client->post($apiUrl, [
        "headers" => [
          "client_id" => $muleSoftConfigApi['client_id_magic_link'],
          "client_secret" => $muleSoftConfigApi['client_secret_magic_link'],
          "Accept" => 'application/json',
          "Content-Type" => 'application/json',
          'x-correlation-id' => $transactionId
        ],
        'json' => $data_event['data_crm'],
      ]);
      $response = json_decode($request->getBody(), true);
      $this->logger->get('api_log')->info(
        '<pre><code>' . print_r([
          'x-correlation-id' => $transactionId,
          'url' => $apiUrl,
          'data' => $data_event['data_crm'],
          'response' =>$response,
        ], TRUE) . '<pre><code>'
      );

      $attendees = $data_event['data_crm']['registerAttendees']['attendeesList'];

      $magic_link_arr = [];
      foreach ($attendees as $attendee) {
        $event = Node::load($event_nid);
        $email = $attendee['email'];
        $user = user_load_by_mail($email);
        $reponse_magic_link = $this->getLinkEvent([
          'webinarId' => $data_event['data_crm']['registerAttendees']['webinarId'],
          'user' => $user,
        ]);
        if (isset($reponse_magic_link['loginURL']) && $reponse_magic_link['loginURL']) {
          $magic_link = $reponse_magic_link['loginURL'];
          $event_magic_link = $user->get('field_event_magic_link')->getValue() ?? [];
          $event_magic_link[] = [
            'target_id' => $event_nid,
            'value' => $magic_link,
          ];
          $magic_link_arr[$data_event['data_crm']['registerAttendees']['webinarId'] . '_' . $user->field_takeda_id->value] = $magic_link;
          $user->set('field_event_magic_link', $event_magic_link)->save();
        }
        $data['event'] = $event;
        if ($send_mail) {
          \Drupal::service('plugin.manager.mail')->mail('spotme_events',
            'register_event_success',
            $user->getEmail(),
            $user->getPreferredLangcode(),
            ['account' => $user] + $data
          );
        }
      }

      foreach ($data_event['data_mca'] as $data_mca) {
        if (isset($magic_link_arr[$data_mca['webinarId'] . '_' . $data_mca['takeda_id']])) {
          $data_mca['magicLink'] = $magic_link_arr[$data_mca['webinarId'] . '_' . $data_mca['takeda_id']];
          unset($data_mca['takeda_id']);
        }
        $this->pushDataMca($data_mca);
      }

      return $response;

    } catch (RequestException $e) {
      $this->logger->get('api_log')->error(
        '<pre><code>' . print_r([
          'x-correlation-id' => $transactionId,
          'url' => $apiUrl,
          'data' => $data_event['data_crm']['registerAttendees'],
          'message' => str_replace("\n", "", $e->getMessage())
        ], TRUE) . '<pre><code>'
      );
      $attendees = $data_event['data_crm']['registerAttendees']['attendeesList'];
      foreach ($attendees as $attendee) {
        $email = $attendee['email'];
        $user = user_load_by_mail($email);
        $registered_events = $user->get('field_registered_event')->getValue() ?? [];
        $key = array_search($event_nid, array_column($registered_events, 'target_id'));
        if ($key || $key === 0) {
          unset($registered_events[$key]);
          $user->set('field_registered_event', $registered_events)->save();
        }
        $event = Node::load($event_nid);
        $data['user'] = $user;
        $data['event'] = $event;
        if ($send_mail) {
          \Drupal::service('plugin.manager.mail')->mail('spotme_events',
            'register_event_fail',
            $user->getEmail(),
            $user->getPreferredLangcode(),
            ['account' => $user] + $data
          );
        }
      }

    }
    return $response;

  }

  /**
   * {@inheritdoc }
   */
  public function pushDataMca($data_event) {
    if (empty($data_event)) {
      return [];
    }

    // Mulesoft API config.
    $muleSoftConfigApi = $this->getMuleSoftApiConfig();

    $apiUrl = $muleSoftConfigApi['url_endpoint'] . "/multichannelActivity";

    // Transaction id.
    $transactionId = $this->generateTransactionApiId('mca_register_event', $data_event['webinarId']);

    // Call API get Data.
    $client = \Drupal::httpClient();
    $response = [];
    try {
      $request = $client->post($apiUrl, [
        "headers" => [
          "client_id" => $muleSoftConfigApi['client_id'],
          "client_secret" => $muleSoftConfigApi['client_secret'],
          "Accept" => 'application/json',
          "Content-Type" => 'application/json',
          'x-correlation-id' => $transactionId
        ],
        'json' => [$data_event],
      ]);
      $response = json_decode($request->getBody(), true);
      $this->logger->get('api_log')->info(
        '<pre><code>' . print_r([
          'x-correlation-id' => $transactionId,
          'url' => $apiUrl,
          'data' => $data_event,
          'response' =>$response,
        ], TRUE) . '<pre><code>'
      );
      return $response;

    } catch (RequestException $e) {
      $this->logger->get('api_log')->error(
        '<pre><code>' . print_r([
          'x-correlation-id' => $transactionId,
          'url' => $apiUrl,
          'data' => $data_event,
          'message' => str_replace("\n", "", $e->getMessage())
        ], TRUE) . '<pre><code>'
      );
    }
    return $response;
  }

  /**
   * {@inheritdoc }
   */
  public function getMuleSoftApiConfig() {
    $config = $this->configFactory->get('spotme.mulesoft_api_config');
    return [
      'url_endpoint' => $config->get('url_endpoint'),
      'client_id' => $config->get('client_id'),
      'client_secret' => $config->get('client_secret'),
      'org_id' => $config->get('org_id'),
      'url_endpoint_magic_link' => $config->get('url_endpoint_magic_link'),
      'client_id_magic_link' => $config->get('client_id_magic_link'),
      'client_secret_magic_link' => $config->get('client_secret_magic_link'),
    ];
  }

  /**
   * {@inheritdoc }
   */
  public function generateTransactionApiId($type, $takeda_id) {
    $transactionId = $takeda_id . '-' . strtotime('now');
    if ($type) {
      $transactionId = strtoupper($type[0]) . '-' . $transactionId;
    }
    return $transactionId;
  }

  /**
   * {@inheritdoc }
   */
  public function getMachineName($string) {
    return preg_replace('@[^A-Za-z0-9\-]+@', '', $string);
  }

  public function getNodeIdByEventId($event_id) {
    $sql = \Drupal::database()->select('node__field_event_id', 'fei');
    $sql->fields('fei', ['entity_id']);
    $sql->condition('fei.field_event_id_value', $event_id);
    return $sql->execute()->fetchField();
  }

}
