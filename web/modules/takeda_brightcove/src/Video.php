<?php

declare(strict_types=1);

namespace Drupal\takeda_brightcove;

use Drupal\views\Plugin\views\field\Boolean;

/**
 * Class representing video data from Brightcove.
 *
 * Note that more data may exist in the upstream Brightcove service, such as
 * when this class is constructed with an ID directly instead of calling
 * fromJson(). In that case, use \Drupal\takeda_brightcove\VideoFactory to
 * load the full data (at the cost of a remote HTTP call).
 */
class Video {

  /**
   * The player to use for videos.
   */
  const PLAYER_ID = 'default';

  /**
   * The account ID for Brightcove.
   *
   * @var string
   */
  private $accountId;

  /**
   * The ID of the video.
   *
   * @var string
   */
  private $videoId;


  /**
   * If the video is an experience
   *
   * @var boolean
   */
  private $isExperience;


  /**
   * The name of the video.
   *
   * @var string
   */
  private $name = '';

  /**
   * The length of the video.
   *
   * @var string
   */
  private $length = '';

  /**
   * The URL of the poster of the video.
   *
   * @var string
   */
  private $posterSource = '';

  /**
   * Video constructor.
   *
   * @param string $video_id
   *   The ID of this video.
   */
  public function __construct(string $video_id, $isExperience = false) {
    $this->videoId = $video_id;
    $this->isExperience = $isExperience;
    $this->accountId = \Drupal::config('takeda_brightcove.settings')->get('account_id');
  }

  /** 
   * Construct a video from a BrightCove API response.
   *
   * @param array $json
   *   An array of JSON data.
   *
   * @return static
   *   A new video.
   *
   * @see \Drupal\takeda_brightcove\VideoFactory
   */
  public static function fromJson(array $json): self {
    $video = new Video($json['id']);
    $video->setName($json['name']);

    if (!isset($json['isExperience']) && isset($json['poster_sources']['0']['src'])) {
      $video->setPosterSource($json['poster_sources']['0']['src']);
    }

    return $video;
  }

  /**
   * Construct a video from a studio URL.
   *
   * @param string $url
   *   The studio URL.
   *
   * @return static
   *   A new video.
   */
  public static function fromStudioUrl(string $url): self {

    if (strpos($url, 'experiences') !== false) {
      $segments = explode('/', $url);
      $video_id = array_slice($segments, -2, 1)[0];
      $isExperience = true;
    } else {
      $isExperience = false;
      $video_id = str_replace(
        'https://studio.brightcove.com/products/videocloud/media/videos/',
        '',
        $url
      );
    }
    return new self($video_id, $isExperience);
  }


  /**
   * Construct a video from an experience URL.
   *
   * @param string $url
   *   The studio URL.
   *
   * @return static
   *   A new video.
   */
  public static function fromExperienceUrl(string $url): self
  {

    if (strpos($url, 'experiences') !== false) {
      $segments = explode('/', $url);
      $video_id = array_slice($segments, -2, 1)[0];
      $isExperience = true;
    } else {
      $isExperience = false;
      $video_id = str_replace(
        'https://studio.brightcove.com/products/videocloud/media/videos/',
        '',
        $url
      );
    }
    return new self($video_id, $isExperience);
  }


  /**
   * Retrieve the embed code source for a given video ID.
   *
   * @return string
   *   The source to use in an iframe.
   */
  public function getEmbedSrc() {
    $player_id = static::PLAYER_ID;
    return "https://players.brightcove.net/{$this->accountId}/{$player_id}_default/index.html?videoId={$this->videoId}";
  }

  /**
   * Retrieve the script code source for a given video ID.
   *
   * @return string
   *   The source to use in an iframe.
   */
  public function getScriptSrc()
  {
    $player_id = static::PLAYER_ID;
    return "https://players.brightcove.net/{$this->accountId}/{$player_id}_default/index.js";
  }

  /**
   * Retrieve the script code source for a given experience ID.
   *
   * @return string
   *   The source to use in an iframe.
   */
  public function getExperienceSrc()
  {
    $player_id = static::PLAYER_ID;
    $experienceId = $this->videoId;
    return "https://players.brightcove.net/{$this->accountId}/experience_{$experienceId}/live.js";
  }

  /**
   * Retrieve the player ID.
   *
   * @return string
   *   The source to use in an iframe.
   */
  public function getPlayerId()
  {
    $player_id = static::PLAYER_ID;
    return $player_id;
  }

  /**
   * Retrieve the account ID.
   *
   * @return string
   *   The source to use in an iframe.
   */
  public function getAccountId()
  {
    return $this->accountId;
  }



  /**
   * Get the name of the video.
   *
   * @return string
   *   The name of the video.
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * Set the name of the video.
   *
   * @param string $name
   *   The name to set.
   */
  public function setName(string $name): void {
    $this->name = $name;
  }

  /**
   * Get the duration of the video.
   *
   * @return string
   *   The duration of the video.
   */
  public function getDuration(): string
  {
    return $this->duration;
  }

  /**
   * Set the duration of the video.
   *
   * @param string $duration
   *   The duration to set.
   */
  public function setDuration(string $duration): void
  {
    $this->duration = $duration;
  }

  /**
   * Get the poster source URL.
   *
   * @return string
   *   The poster source URL.
   */
  public function getPosterSource(): string {
    return $this->posterSource;
  }

  /**
   * Set the source URL of the poster.
   *
   * @param string $poster
   *   The URL to set.
   */
  public function setPosterSource(string $poster): void {
    $this->posterSource = $poster;
  }

  /**
   * Return the video ID.
   *
   * @return string
   *   The video ID.
   */
  public function getId(): string {
    return $this->videoId;
  }

  /**
   * Return if the video is an experience
   *
   * @return bool
   *   If it is an experience
   */
  public function getIsExperience(): bool
  {
    return $this->isExperience;
  }

}
