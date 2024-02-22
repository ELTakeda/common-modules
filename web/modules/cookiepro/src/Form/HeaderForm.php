<?php

namespace Drupal\cookiepro\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provide settings page for adding cookiepro header scripts.
 */
class HeaderForm extends ConfigFormBase {

  /**
   * Implements FormBuilder::getFormId.
   */
  public function getFormId() {
    return 'hfs_header_settings';
  }

  /**
   * Implements ConfigFormBase::getEditableConfigNames.
   */
  protected function getEditableConfigNames() {
    return ['cookiepro.header.settings'];
  }

  /**
   * Implements FormBuilder::buildForm.
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $header_section = $this->config('cookiepro.header.settings')->get();

    $form['hfs_header']['description'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Getting Started with CookiePro'),
      '#description' => $this->t('<p>The CookiePro module requires a CookiePro account. To sign up for a free or paid account, visit <a href="https://www.cookiepro.com/pricing/?referral=DRUPMOD" target="_blank">CookiePro.com</a> and select the edition that fits your business needs.</p>
<p>Once you have access to your CookiePro account, follow the steps below or check out the <a href="https://www.cookiepro.com/help/technical-implementation/" target="_blank">Getting Started</a> guide to get CookiePro up and running on your Drupal website.</p>
<h4>Cookie Banner & Preference Center</h4>
<p>1. Scan your website and review your cookies categories</p>
<p>2. Style and configure your banner and preference center</p>
<p>3. Block cookies using a tag manager and/or JS Rewrite</p>
<p>4. Copy and paste the Main Cookies Script Tag below</p>
<p>5. Save the configuration to publish the cookie banner</p>
<p>Your banner is now viewable on your Drupal website! Detailed step-by-step instructions and best practices are available in the <a href="https://community.cookiepro.com" target="_blank">CookiePro Community</a>.</p>'),
      '#open' => TRUE,
    ];

    $form['hfs_header']['scripts'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Insert the Main Script Tag from CookiePro here. Remove the comments at the start and end of the script and then save the configuration.'),
      '#default_value' => $header_section['scripts'] ?? '',
      '#rows' => 10,
    ];

    $form['hfs_header']['footer'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Additional Information'),
      '#description' => $this->t('<p>In addition to your cookie banner and preference center, CookiePro automatically generates the following scripts that can be added to your Cookie or Privacy Notice page.</p>
<p>1. The Cookie Settings script inserts a button on your site to enable visitors to access their cookie preferences at any time</p>
<p>2. The Cookie List script inserts a detailed list of cookies, including descriptions and categories they are assigned to</p>
<h4> Disclaimer </h4>
<p>This module allows you to publish CookiePro’s cookie banner and preference center on your Drupal website. Use of this module does not, by itself, ensure compliance with legal requirements related to cookies.</p>'),
      '#open' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Implements FormBuilder::submitForm().
   *
   * Serialize the user's settings and save it to the Drupal's config Table.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $this->configFactory()
      ->getEditable('cookiepro.header.settings')
      ->set('scripts', $values['scripts'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
