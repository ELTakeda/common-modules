<?php

declare(strict_types=1);

namespace Drupal\takeda_brightcove\Plugin\media\Source;

use Drupal\takeda_brightcove\VideoFactory;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\File\Exception\FileException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Utility\Error;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Exception\TransferException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a media source plugin for Brightcove videos.
 *
 * @MediaSource(
 *   id = "tak_brightcove_exp",
 *   label = @Translation("Brightcove Experience"),
 *   description = @Translation("Share URL for a Brightcove Experience."),
 *   allowed_field_types = {
 *     "link",
 *     "string",
 *     "string_long",
 *   },
 *   default_thumbnail_filename = "experience.png",
 *   deriver = "",
 *   providers = {},
 *   media_type_metadata_attribute = "media_type_value",
 *   forms = {
 *     "media_library_add" = "Drupal\takeda_brightcove\Form\AddExperienceForm",
 *   },
 * )
 */
class BrightcoveExp extends MediaSourceBase {

  /**
   * The filesystem service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  private $fileSystem;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * The factory used to load remote video data.
   *
   * @var \Drupal\takeda_brightcove\VideoFactory
   */
  private $videoFactory;

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  private $httpClient;

  /**
   * BrightcoveExp constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   Entity field manager service.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_manager
   *   The field type plugin manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\takeda_brightcove\VideoFactory $video_factory
   *   The video factory service.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The filesystem service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The system logger.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The system messenger.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager,
    FieldTypePluginManagerInterface $field_type_manager,
    ConfigFactoryInterface $config_factory,
    VideoFactory $video_factory,
    ClientInterface $http_client,
    FileSystemInterface $file_system,
    LoggerInterface $logger,
    MessengerInterface $messenger
  ) {
    $this->videoFactory = $video_factory;
    $this->fileSystem = $file_system;
    $this->logger = $logger;
    parent::__construct(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $entity_type_manager,
      $entity_field_manager,
      $field_type_manager,
      $config_factory
    );
    $this->messenger = $messenger;
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('config.factory'), 
      $container->get('takeda_brightcove.video_factory'),
      $container->get('http_client'),
      $container->get('file_system'),
      $container->get('logger.factory')->get('media'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadataAttributes() {

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadata(MediaInterface $media, $attribute_name) {
    $source_field_value = $this->getSourceFieldValue($media);

    // dd('get metadata', $media);
    switch ($attribute_name) {
      case 'default_name':
        return $this->getVideoName($source_field_value) ?: parent::getMetadata(
          $media,
          'default_name'
        );

      case 'thumbnail_uri':
        return $this->getLocalThumbnailUri(
          $source_field_value
        ) ?: parent::getMetadata($media, 'thumbnail_uri');
    }

    return NULL;
  }

  /**
   * Return the name of the video.
   *
   * @param string $source_field_value
   *   The studio URL.
   *
   * @return string|null
   *   The name of the video, or NULL if one can't be found.
   */
  private function getVideoName(string $source_field_value): ?string {
    try {
      $video = $this->videoFactory->loadFromStudioUrl($source_field_value);
      return $video->getName();
    }
    catch (GuzzleException $e) {
      $this->messenger()->addError(
        $this->t(
          'Unable to retrieve the name of the Brightcove video. Check the Brightcove Studio URL and try again.'
        )
          );
      $this->watchdogException($e);
      return NULL;
    }
  }

  /**
   * Copy of \watchdog_exception().
   *
   * @param \Exception $exception
   *   The exception that is going to be logged.
   * @param string $message
   *   The message to store in the log. If empty, a text that contains all
   *   useful information about the passed-in exception is used.
   * @param array $variables
   *   Array of variables to replace in the message on display or
   *   NULL if message is already translated or not possible to
   *   translate.
   * @param int $severity
   *   The severity of the message, as per RFC 3164.
   * @param string $link
   *   A link to associate with the message.
   */
  private function watchdogException(
    \Exception $exception,
    string $message = NULL,
    array $variables = [],
    int $severity = RfcLogLevel::ERROR,
    string $link = NULL
  ) {
    // Use a default value if $message is not set.
    if (empty($message)) {
      $message = '%type: @message in %function (line %line of %file).';
    }

    if ($link) {
      $variables['link'] = $link;
    }

    $variables += Error::decodeException($exception);

    $this->logger->log($severity, $message, $variables);
  }

  /**
   * Returns the local URI for a resource thumbnail.
   *
   * If the thumbnail is not already locally stored, this method will attempt
   * to download it. This is based on the oEmbed method.
   *
   * @param string $source_field_value
   *   The source field value.
   *
   * @return string|null
   *   The local thumbnail URI, or NULL if it could not be downloaded, or if the
   *   resource has no thumbnail at all.
   */
  private function getLocalThumbnailUri($source_field_value): ?string {
    try {
      $video = $this->videoFactory->loadFromStudioUrl($source_field_value);
      $remote_thumbnail_url = $video->getPosterSource();
    }
    catch (TransferException | InvalidArgumentException $e) {
      $this->messenger()->addError(
        $this->t(
          'Unable to retrieve the thumbnail of the Brightcove video. Check the Brightcove Studio URL and try again.'
        )
          );
      $this->watchdogException($e);
      return '';
    }

    // If there is no remote thumbnail, there's nothing for us to fetch here.
    if (!$remote_thumbnail_url) {
      return NULL;
    }

    // Compute the local thumbnail URI, regardless of whether or not it exists.
    $directory = 'public://brightcove_remote_media_thumbnails';
    $local_thumbnail_uri = $this->generateLocalThumbnailUri(
      $remote_thumbnail_url,
      $directory
    );

    // If the local thumbnail already exists, return its URI.
    if (file_exists($local_thumbnail_uri)) {
      return $local_thumbnail_uri;
    }

    if (!$this->createThumbnailDirectory($directory)) {
      return NULL;
    }

    try {
      // Save the image string as a local file.
      $image = $this->httpClient->get($remote_thumbnail_url)->getBody();
      $this->fileSystem->saveData(
        $image,
        $local_thumbnail_uri,
        FileSystemInterface::EXISTS_REPLACE
      );
      return $local_thumbnail_uri;
    }
    catch (GuzzleException $e) {
      $this->logger->warning($e->getMessage());
    }
    catch (FileException $e) {
      $this->logger->warning(
        'Could not download remote thumbnail from {url}.',
        [
          'url' => $remote_thumbnail_url,
        ]
      );
    }
    return NULL;
  }

  /**
   * Generate a local thumbnail URI.
   *
   * @param string $remote_thumbnail_url
   *   The remote thumbnail URL.
   * @param string $directory
   *   The directory to save the video thumbnail in.
   *
   * @return string
   *   The generated local URL.
   */
  private function generateLocalThumbnailUri(
    string $remote_thumbnail_url,
    string $directory
  ): string {
    // Remove parameters from the URL before generating a local filename.
    $remote_thumbnail_url_parsed = parse_url($remote_thumbnail_url);
    unset($remote_thumbnail_url_parsed['query']);
    $remote_thumbnail_url_filename = http_build_query(
      $remote_thumbnail_url_parsed
    );
    // Generate a local URI.
    $local_thumbnail_uri = "$directory/" . Crypt::hashBase64(
        $remote_thumbnail_url_filename
      ) . '.' . pathinfo($remote_thumbnail_url_filename, PATHINFO_EXTENSION);
    return $local_thumbnail_uri;
  }

  /**
   * Create a thumbnail directory if required.
   *
   * @param string $directory
   *   The directory to create.
   *
   * @return bool
   *   TRUE if successful, FALSE otherwise.
   */
  private function createThumbnailDirectory(string $directory): bool {
    // The local thumbnail doesn't exist yet, so try to download it. First,
    // ensure that the destination directory is writable, and if it's not,
    // log an error and bail out.
    if (!$this->fileSystem->prepareDirectory(
      $directory,
      FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS
    )) {
      $this->logger->warning(
        'Could not prepare thumbnail destination directory @dir for Brightcove videos.',
        [
          '@dir' => $directory,
        ]
      );
      return FALSE;
    }
    return TRUE;
  }

}
