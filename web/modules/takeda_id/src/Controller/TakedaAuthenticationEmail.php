<?php
namespace Drupal\takeda_id\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\user\UserInterface;
class TakedaAuthenticationEmail extends ControllerBase {
  public function userData(AccountInterface $user = NULL, Request $request) {
    $first_name = implode('', $user->get('field_first_name')->getValue()[0]);
    $last_name = implode('', $user->get('field_last_name')->getValue()[0]);
    $uid =  implode('',$user->get('uid')->getValue()[0]);
if(isset($_POST) && !empty($_POST)){
    $verificationMail = \Drupal::service('plugin.manager.mail')->mail($module = 'takeda_id', $key = 'register_confirmation', $user->getEmail(), $user->getPreferredLangcode(), ['account' => $user], $reply = NULL, $send = TRUE);
    \Drupal::messenger()->addStatus($this->t('Email to user '.$first_name." ".$last_name.' was send successfully')); 
  }   
    $user_data = [
      'first_name' => $first_name,
      'last_name' => $last_name,
      'uid' => $uid,
    ];
    $userData = \Drupal::service('user.data');
    $token = $userData->get('takeda_id', $uid, 'recovery_token');
   ;
    if ($token) {
      $user_data['verify'] = takeda_id_generate_confirmation_url($user, $token, []);
    }
    $build = [
        '#theme' => 'authemail',
        '#context' => [
            'user_data' => $user_data,
          ],
      ];

    return $build;
  }

 public function takeda_id_generate_confirmation_url(UserInterface $account, $code, array $options = [])
{
  $timestamp = \Drupal::time()->getRequestTime();
  $langcode = isset($options['langcode']) ? $options['langcode'] : $account->getPreferredLangcode();

  return \Drupal::service('url_generator')->generateFromRoute(
    'takeda_id.verify',
    [
      'user' => $account->uuid->value,
      'timestamp' => $timestamp,
      'hash' => user_pass_rehash($account, $timestamp),
      'code' => $code
    ],
    [
      'absolute' => TRUE,
      'language' => \Drupal::languageManager()->getLanguage($langcode),
    ]
  );
}
}