<?php

namespace Drupal\takeda_brightcove\Form;

use Drupal\takeda_brightcove\Plugin\media\Source\BrightcovePod;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\media_library\Form\AddFormBase;
use GuzzleHttp\Exception\ClientException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\media_library\MediaLibraryUiBuilder;
use Drupal\media_library\OpenerResolverInterface;
use Drupal\takeda_brightcove\VideoFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a form to create media entities from Brightcove URLs.
 *
 * @internal
 *   Form classes are internal.
 * @see \Drupal\media_library\Form\OEmbedForm
 */
class AddPodcastForm extends AddFormBase {

  /**
   * The Brightcove video factory service.
   *
   * @var Drupal\takeda_brightcove\VideoFactory
   */
  private $brightcoveVideo;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\media_library\MediaLibraryUiBuilder $library_ui_builder
   *   The media library UI builder.
   * @param \Drupal\media_library\OpenerResolverInterface $opener_resolver
   *   The opener resolver.
   * @param Drupal\takeda_brightcove\VideoFactory $brightcove
   *   Brightcove video factory.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    MediaLibraryUiBuilder $library_ui_builder,
    OpenerResolverInterface $opener_resolver = NULL,
    VideoFactory $brightcove
  ) {
    $this->brightcoveVideo = $brightcove;
    parent::__construct(
      $entity_type_manager,
      $library_ui_builder,
      $opener_resolver
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('media_library.ui_builder'),
      $container->get('media_library.opener_resolver'),
      $container->get('takeda_brightcove.video_factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return $this->getBaseFormId() . '_takeda_brightcove';
  }

  /**
   * {@inheritdoc}
   */
  protected function getMediaType(FormStateInterface $form_state) {
    if ($this->mediaType) {
      return $this->mediaType;
    }

    $media_type = parent::getMediaType($form_state);
    if (!$media_type->getSource() instanceof BrightcovePod) {
      throw new \InvalidArgumentException('Can only add media types which use a Brightcove source plugin.');
    }
    return $media_type;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildInputElement(array $form, FormStateInterface $form_state) {
    // Add a container to group the input elements for styling purposes.
    $form['container'] = [
      '#type' => 'container',
    ];

    $form['container']['url'] = [
      '#type' => 'url',
      '#title' => $this->t('Add @type via URL', [
        '@type' => $this->getMediaType($form_state)->label(),
      ]),
      '#description' => $this->t('Enter your podcast\'s Brightcove studio URL from <a href="https://studio.brightcove.com/">studio.brightcove.com</a>, in the format <em>https://studio.brightcove.com/products/videocloud/media/videos/VIDEOID</em>.'),
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'https://players.brightcove.com/...',
      ],
    ];

    $form['container']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
      '#button_type' => 'primary',
      '#submit' => ['::addButtonSubmit'],
      // @todo Move validation in https://www.drupal.org/node/2988215
      '#ajax' => [
        'callback' => '::updateFormCallback',
        'wrapper' => 'media-library-wrapper',
        // Add a fixed URL to post the form since AJAX forms are automatically
        // posted to <current> instead of $form['#action'].
        // @todo Remove when https://www.drupal.org/project/drupal/issues/2504115
        //   is fixed.
        'url' => Url::fromRoute('media_library.ui'),
        'options' => [
          'query' => $this->getMediaLibraryState($form_state)->all() + [
            FormBuilderInterface::AJAX_FORM_REQUEST => TRUE,
          ],
        ],
      ],
    ];
    return $form;
  }

  /**
   * Submit handler for the add button.
   *
   * @param array $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function addButtonSubmit(array $form, FormStateInterface $form_state) {
    $this->processInputValues([$form_state->getValue('url')], $form, $form_state);
  }

  /**
   * Validate brightcove url.
   *
   * @param array $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->hasValue('url')) {
      $url = $form_state->getValue('url');
      if (!empty($url)) {
        $is_url = filter_var($url, FILTER_VALIDATE_URL);
        $segments = explode('/', $url);
        $video_id = end($segments);
        if ($is_url && is_numeric($video_id)) {
          try {
            $this->brightcoveVideo->load($video_id);
          }
          catch (ClientException $ce) {
            $form_state->setErrorByName('bad_url', $this->t('Video not found.'));
          }
        }
        else {
          $form_state->setErrorByName('bad_url', $this->t('The url you entered is incorrect. Add a well formatted url with a valid Brightcove video ID.'));
        }
      }
      else {
        $form_state->setErrorByName('missing_url', $this->t('Please enter a valid url.'));
      }
    }
  }

}
