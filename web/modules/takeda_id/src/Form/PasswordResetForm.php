<?php

namespace Drupal\takeda_id\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\takeda_id\TakedaIdInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Implements the Password Reset form.
 */
class PasswordResetForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'takeda_id_password_reset_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $session = \Drupal::request()->getSession();
    $takeda_id = $session->get('user_id');
    $hashedPassword = $session->get('hashedPassword');

    if (empty($takeda_id) || empty($hashedPassword)) {
        $messengerService = \Drupal::service('messenger');
        $messengerService->addError(t('Unable to reach desired page.'));
        
        $url = Url::fromRoute('<front>');
        $response = new RedirectResponse($url->toString());
        return $response->send();
    }

    $form['heading'] = [
        '#markup' => '<h4>' . $this->t('Your password has been expired. Please fill the form to continue.') . '</h4>',
        '#prefix' => '<div class="form-heading">',
        '#suffix' => '</div>',
    ];

    $form['#attached'] = [
        'library' => [
            'takeda_id/reset-form-styling',
            'core/jquery'
        ],
    ];

    // Wrap the form in a custom class.
    $form['#prefix'] = '<div class="custom-form-wrapper">';
  

    $form['old_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Current Password'),
      '#required' => TRUE,
      '#field_suffix' => '<i class="eye" id="old_password"></i>',
    ];

    $form['password1'] = array(
        '#type' => 'password',
        '#title' => $this->t('New Password'),
        '#required' => TRUE,
        '#field_suffix' => '<i class="eye" id="password1"></i>',
        '#attributes' => array(
          'class' => array('password-criteria-check'),
        ),
        '#attached' => array(
          'library' => array('takeda_id/password-validation'),
        ),
    );

    $form['password1_criteria'] = array(
        '#type' => 'markup',
        '#markup' => '
            <ul class="password-validation-info password-criteria-box">' .
                '<li class="js-validation-info-length validation-icon-invalid">At least 8 characters long</li>' .
                '<li class="js-validation-info-capital validation-icon-invalid">At least one uppercase letter</li>' .
                '<li class="js-validation-info-letter validation-icon-invalid">At least one lowercase letter</li>' .
                '<li class="js-validation-info-number validation-icon-invalid">At least one number character</li>' .
                '<li class="js-validation-info-special validation-icon-invalid">At least one special character</li>' .
            '</ul>',
    );

    $form['password2'] = array(
        '#type' => 'password',
        '#title' => $this->t('Confirm Password'),
        '#required' => TRUE,
        '#field_suffix' => '<i class="eye" id="password2"></i>',
    );

    $form['password_confirmation_message'] = array(
        '#type' => 'markup',
        '#prefix' => '<div id="password-confirmation-message">',
        '#suffix' => '</div>',
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Update Password'),
        '#attributes' => array(
            'class' => array('force-submit'),
            'disabled' => 'disabled'
          ),
    );

    $form['#suffix'] = '</div>';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $messengerService = \Drupal::service('messenger');
    $session = \Drupal::request()->getSession();

    $takeda_id = $session->get('user_id');
    $hashedPassword = $session->get('hashedPassword');

    $values = $form_state->getValues();

    if (password_verify($values['old_password'], $hashedPassword)) {
        if ($values['old_password'] == $values['password2']) {
            $messengerService->addError($this->t('Your new password match the current one. Please use different password from the current one.'));
            $session->remove('user_id');
            $session->remove('hashedPassword');
            return $form_state->setRedirect('user.login');
        }
        $password_credentials = array(
            'oldPassword' => array('value' => $values['old_password']),
            'newPassword' => array('value' => $values['password2'])
        );
    } else {
        $messengerService->addError($this->t('Incorrect password.'));
        $session->remove('user_id');
        $session->remove('hashedPassword');
        return $form_state->setRedirect('user.login');
    }

    $session->remove('hashedPassword');

    try {

        $client = \Drupal::httpClient();
        $config = \Drupal::config(TakedaIdInterface::CONFIG_OBJECT_NAME);

        $request = $client->post($config->get('api_url') . "/user/changePassword/userId/" . $takeda_id, [
            "headers" => [
            "client_id" => $config->get('api_key'),
            "client_secret" => $config->get('api_secret'),
            "Content-Type" => "application/json",
            ],
    
            'json' => $password_credentials
        ]);

        $messengerService->addMessage(t('Your password has been updated. Please log-in with your new credentials.'));
        
    } catch (RequestException $e) {
        watchdog_exception('takeda_id', $e); 
    }

    $session->remove('user_id');
    $form_state->setRedirect('user.login');
  }
}