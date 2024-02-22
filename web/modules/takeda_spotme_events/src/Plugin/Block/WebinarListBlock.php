<?php

namespace Drupal\spotme_events\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\spotme_events\TwigExtension;
use Drupal\views\Views;

/**
 * Provides Webinar list.
 *
 * @Block(
 *   id = "spotme_custom_webinar_list_block",
 *   admin_label = @Translation("Webinar list block"),
 * )
 */
class WebinarListBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $list_views = TwigExtension::getListViewWebinar('block');
    $list_views_page = TwigExtension::getListViewWebinar();
    $data = [];
    if ($list_views) {
      foreach ($list_views as $view_key => $view_arr) {
        $view = Views::getView('webinar_event');
        $view_page_key = str_replace('_block', '_page', $view_key);
        $view->setDisplay($view_key);
        $view->execute();
        $data[$view_key]['title'] = $view_arr['display_title'];
        if (isset($list_views_page[$view_page_key])) {
          $data[$view_key]['path'] = $list_views_page[$view_page_key]['display_options']['path'];
        }
        $data[$view_key]['html'] = @\Drupal::service('renderer')->render($view->render());
      }
    }
    return [
      '#theme' => 'spotme_custom_webinar_list_block',
      '#data' => $data,
    ];
  }

}
