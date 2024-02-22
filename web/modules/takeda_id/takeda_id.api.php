<?php

/**
 * @file
 * Describes hooks provided by the Takeda ID module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Act on user account verification.
 *
 * This hook is invoked from takeda_id_form_user_register_submit() once a user's account
 * has been verified and registered with Okta.
 *
 * The User account is activated when allowed by the requireLeadConfirmation setting,
 * the local Drupal password has been cleared, and the takeda_id_unverified role is removed.
 *
 * @param Drupal\user\Entity\User $account
 *   The user object on which the operation is being performed.
 */

function hook_takeda_account_set_verified($account) {
    // Perform desired local module actions
    \Drupal::logger('my_module')->notice('Takeda Id account ' . $account->id() . ' has been verified');
}


/**
 * Act on user account pending HCP validation.
 *
 * This hook is invoked from takeda_id_form_user_register_submit() once a lead has been requested
 * and a valid match has not been automatically found. 
 *
 * The User account is blocked when required by the require_hcp_match_to_activate setting,
 * and the takeda_id_pending role has been assigned to the user.
 *
 * @param Drupal\user\Entity\User $account
 *   The user object on which the operation is being performed.
 */

function hook_takeda_account_set_hcp_pending($account) {
    // Perform desired local module actions
    \Drupal::logger('my_module')->notice('Takeda Id account ' . $account->id() . ' is pending HCP validation');

    // Get associated Lead ID 
    $userData = \Drupal::service('user.data');
    $leadId = $userData->get('takeda_id', $account->id(), 'lead_id');
}


/**
 * Act on user account HCP Matched.
 *
 * This hook is invoked from takeda_id_form_user_register_submit() or the leadCallback() once the user is
 * verified as a valid HCP.
 *
 * The User account is activated when required, the takeda_id_active role is added to the user,
 * the Takeda Customer ID is been added to the user data object, and user consent is submitted where
 * enabled by the enable_takeda_id_consent setting.
 *
 * @param Drupal\user\Entity\User $account
 *   The user object on which the operation is being performed.
 */

function hook_takeda_account_set_hcp_matched($account) {
    // Perform desired local module actions
    \Drupal::logger('my_module')->notice('Takeda Id account ' . $account->id() . ' is marked as a valid HCP');

    // Get associated Customer ID 
    $userData = \Drupal::service('user.data');
    $customerId = $userData->get('takeda_id', $account->id(), 'customer_id');

    // Perform actions that require the user's customer ID, such as submission of additional consent handling
    // (eg. to OneTrust)

    // Get Consent Captured Timestamp
    $consentCaptured = $userData->get('takeda_id', $account->id(), 'consent_captured');
}


/**
 * Act on user account HCP Rejected.
 *
 * This hook is invoked from the leadCallback() when the user is rejected as a HCP. 
 *
 * The User account is blocked when required by the block_rejected_leads setting,
 * and the takeda_id_non_hcp role is been assigned to the user.
 *
 * @param Drupal\user\Entity\User $account
 *   The user object on which the operation is being performed.
 */

function hook_takeda_account_set_hcp_rejected($account) {
    // Perform desired local module actions
    \Drupal::logger('my_module')->notice('Takeda Id account ' . $account->id() . ' is marked as a non-HCP');
}

/**
 * Act on existing Takeda ID account registered to Drupal  
 *
 * This hook is invoked from takeda_account_register_existing_user() / takeda_id_authenticate()
 * when an existing Takeda ID user logs in to the Drupal site, creating in a new Drupal Account.
 *
 *
 * @param Drupal\user\Entity\User $account
 *   The new user account that has been created.
 */

function hook_takeda_account_register_existing_user($account) {
    // Perform desired local module actions
    \Drupal::logger('my_module')->notice('Takeda Id account ' . $account->id() . ' has logged into the service for the first time');
}


/**
 * @} End of "addtogroup hooks".
 */
