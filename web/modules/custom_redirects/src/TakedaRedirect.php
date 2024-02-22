<?php

namespace Drupal\custom_redirects;

use \Symfony\Component\HttpFoundation\RedirectResponse;

class TakedaRedirect {

    public function common_modules_redirect($string_get){

      $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();

      if ( $string_get == 'fail' ){
        $lp_nids = \Drupal::entityQuery('node')
                          ->condition('type', 'login_page')
                          ->accessCheck(TRUE) 
                          ->execute(); 
        $last_lp_id = end($lp_nids);

        $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $last_lp_id);
      } elseif ( $string_get == 'email_activated' ){
        $eap_nids = \Drupal::entityQuery('node')
                            ->condition('type', 'email_activated_page')
                            ->accessCheck(TRUE) 
                            ->execute(); 
        $last_eap_id = end($eap_nids);

        $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $last_eap_id);
      } else {
        $elp_nids = \Drupal::entityQuery('node')
                            ->condition('type', 'expired_link_page')
                            ->accessCheck(TRUE) 
                            ->execute(); 
        $last_elp_id = end($elp_nids);

        $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $last_elp_id);
      }

      $host = \Drupal::request()->getHost();

      $alias = '/' . $lang . $alias;

      if ( $host == 'localhost' ){
        $url = '/common_modules/web' . $alias;
      } else {
        $url = $alias;
      }

      if ( $string_get == 'fail' ){
        $url = $url . '?login=failed';
      }
      
      $response = new RedirectResponse($url);
      $response->send();
      return;
  }

}