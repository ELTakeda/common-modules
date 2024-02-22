<?php

declare(strict_types=1);

namespace Drupal\takeda_brightcove\Plugin\Field\FieldFormatter;

use Drupal\takeda_brightcove\Video;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;


/**
 * Plugin implementation of the 'brightcove_exp_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "brightcove_exp_formatter",
 *   label = @Translation("Brightcove Experience Player"),
 *   field_types = {
 *     "link",
 *     "string",
 *     "string_long",
 *   }
 * )
 */
class BrightcoveExperienceFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return $field_definition->getTargetEntityTypeId() === 'media';
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    foreach ($items as $delta => $item) {
      $media_name = $item->getEntity()->getName();
      if ($item->value) {
        // String fields.
        $video = Video::fromStudioUrl($item->value);
      }
      else {
        // Link fields.
        $video = Video::fromStudioUrl($item->getValue()['uri']);
      }

      $tracking_id = NULL;
      $digital_id = NULL;
      // Check tracking code
      if (\Drupal::currentUser()) {
        /** @var UserDataInterface $userData */
        $userData = \Drupal::service('user.data');
        $digitalId = $userData->get('takeda_id', \Drupal::currentUser()->id(), 'customer_id');
$defaultCountry = $userData->get('takeda_id', \Drupal::currentUser()->id(), 'crm_country');

        if($digitalId) {
          $digital_id = $digitalId;
        }else{
    $digital_id = "NA";
  }
if($defaultCountry) {
      $tracking_id = $defaultCountry;
    }else{
$tracking_id = \Drupal::config('system.date')->get('country')['default'];
    }
    }



      $elements[$delta] = [
        '#theme' => 'takeda_brightcove_experience',
        '#video_src' => $video->getEmbedSrc(),
        '#video_id' => $video->getId(),
        '#script_url' => $video->getExperienceSrc(),
        '#tracking_id' => $tracking_id,
        '#video_name' => $media_name,
        '#digital_id' => $digital_id,
        '#cache' => [
			    'max-age' => 0,
			  ],
      ];
    }
    return $elements;
  }

}
