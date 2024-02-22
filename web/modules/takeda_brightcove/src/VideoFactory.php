<?php

declare(strict_types=1);

namespace Drupal\takeda_brightcove;
use GuzzleHttp\Utils;
use function GuzzleHttp\json_decode;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\ClientInterface;

/**
 * Factory for loading videos from Brightcove.
 */
class VideoFactory {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  private $client;

  /**
   * The account ID to load videos from.
   *
   * @var string
   */
  private $accountId;

    /**
     * The player policy key.
     *
     * Policy keys are public and used in video requests by the player itself.
     * As well, they can never be revoked or changed.
     *
     * @see https://apis.support.brightcove.com/policy/getting-started/overview-policy-api.html
     *
     * @var string.
     */
  private $policyKey;

  /**
   * VideoFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \GuzzleHttp\ClientInterface $client
   *   The HTTP client.
   */
  public function __construct(ConfigFactoryInterface $configFactory, ClientInterface $client) {
    $this->client = $client;
    $config = $configFactory->get('takeda_brightcove.settings');
    $this->accountId = $config->get('account_id');
    $this->policyKey = $config->get('policy_key');
  }

  /**
   * Load a video from Brightcove.
   *
   * @param string $video_id
   *   The video ID to load.
   *
   * @return \Drupal\takeda_brightcove\Video
   *   The loaded video.
   */
  public function load(string $video_id, $isExperience = false): Video {

    if ($isExperience) {
      return $this->loadExperience($video_id);
    }

    $response = $this->client->get(
      "https://edge.api.brightcove.com/playback/v1/accounts/{$this->accountId}/videos/{$video_id}",
      [
        'headers' => [
          'Accept' => 'application/json;pk=' . $this->policyKey,
        ],
      ]
    );

    $data = Utils::jsonDecode($response->getBody()->getContents(), TRUE);
    //$data = json_decode($response->getBody()->getContents(), TRUE);
    return Video::fromJson($data);
  }

  /**
   * Load an experience from Brightcove.
   *
   * @param string $experienceID
   *   The experience ID to load.
   *
   * @return \Drupal\takeda_brightcove\Video
   *   The loaded video.
   */
  public function loadExperience(string $experienceID): Video
  {

    $accountId = $this->accountId;
    $shareUrl = "https://players.brightcove.net/{$accountId}/experience_{$experienceID}/share.html";

    $fp = file_get_contents($shareUrl);
    if (!$fp)
      return null;

    $res = preg_match("/<title>(.*)<\/title>/siU", $fp, $title_matches);
    if (!$res)
      return null;

    $title = preg_replace('/\s+/', ' ', $title_matches[1]);
    $title = trim($title);
  
    if ($title) {
      $data = ['id' => $experienceID, 'account_id' => $this->accountId, 'name' => $title, 'isExperience' => true];
      $video = Video::fromJson($data);
      return $video;
    } else {
      return false;
    }

  }

  /**
   * Reload a loaded video.
   *
   * @param \Drupal\takeda_brightcove\Video $video
   *   The video object to reload.
   *
   * @return \Drupal\takeda_brightcove\Video
   *   The newly loaded video object.
   */
  public function loadFromVideo(Video $video): Video {
    return $this->load($video->getId(), $video->getIsExperience());
  }

  /**
   * Load a video from a Studio URL.
   *
   * @param string $url
   *   The studio URL to parse the video ID to load from.
   *
   * @return \Drupal\takeda_brightcove\Video
   *   The loaded video.
   */
  public function loadFromStudioUrl(string $url): Video {
    $video = Video::fromStudioUrl($url);
    return $this->loadFromVideo($video);
  }



  /**
   * Load a video from an Experience URL.
   *
   * @param string $url
   *   The experience URL to parse the video ID to load from.
   *
   * @return \Drupal\takeda_brightcove\Video
   *   The loaded video.
   */
  public function loadFromExperienceUrl(string $url): Video
  {
    $video = Video::fromExperienceUrl($url);
    return $this->loadFromVideo($video);
  }


}
