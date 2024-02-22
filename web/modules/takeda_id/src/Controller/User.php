<?php

namespace Drupal\takeda_id\Controller;

use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Controller\ControllerBase;

/**
 * Alter User Reset password page. 
 */
class User extends ControllerBase
{

    /**
     * The time service.
     *
     * @var \Drupal\Component\Datetime\TimeInterface
     */
    protected $time;


    /**
     * Displays user password reset form.
     */
    public function resetPass(Request $request, $uid, $timestamp, $hash)
    {

        /** @var \Drupal\user\UserInterface $user */
        $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
        $account = \Drupal::currentUser();

        $session = \Drupal::request()->getSession();
        $userData = \Drupal::service('user.data');

        // The current user is already logged in.
        if ($account->isAuthenticated() && $account->id() == $uid) {
            user_logout();
            // We need to begin the redirect process again because logging out will destroy the session.
            return $this->redirect('user.reset', [
                'uid' => $uid,
                'timestamp' => $timestamp,
                'hash' => $hash,
            ]);
        }

        // Check if the user is registered with Takeda ID

        $authmap = \Drupal::service('externalauth.authmap');
        $authname = $authmap->get($uid, 'takeda_id');

        if ($authname) {
            // User is registered, we need to validate their recovery token
            $recoveryToken = $userData->get('takeda_id', $uid, 'recovery_token');
            $recoveryTokenExp = $userData->get('takeda_id', $uid, 'recovery_token_exp');

            $recoveryTokenExpDate = new \DateTime($recoveryTokenExp);
            $recoveryTokenExpTimestamp = $recoveryTokenExpDate->getTimestamp();
            $currentTimestamp = \Drupal::time()->getRequestTime();

            // Ensure the recovery token hasn't expired on Takeda's side
            if (!$recoveryToken || !$recoveryTokenExp || $currentTimestamp > $recoveryTokenExpTimestamp) {
                \Drupal::messenger()->addError(t('Sorry, your password reset request has timed out. Please try again.'));
                return $this->redirect('user.login');
            }

            // Validate the recovery token and swap for the state token used to perform the reset
            $stateToken = takeda_id_api_verify_recovery_token($recoveryToken);

            if ($stateToken && $stateToken->stateToken) {
                $session->set('takeda_id_state_token', $stateToken->stateToken);
                $session->set('takeda_id_state_token_exp', $stateToken->expiresAt);
            } else {
                // Unable to reset without a state token - abort with error.
                \Drupal::messenger()->addError(t('Sorry, your password reset request has timed out. Please try again.'));
                return $this->redirect('user.login');
            }
        }

        $formObject = \Drupal::service('entity_type.manager')
            ->getFormObject('user', 'default')
            ->setEntity($user);
        return $this->formBuilder()->getForm($formObject);
    }
}
