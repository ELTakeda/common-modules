<?php

namespace Drupal\takeda_id;

/**
 * Provides an interface for Takeda ID constants.
 */
interface TakedaIdInterface {
  /**
   * Takeda ID Config Name
   */
  const CONFIG_OBJECT_NAME = 'takeda_id.settings';

  /**
   * Takeda ID Invitation Info Cookie Names
   * Uses SESS-prefixed cookie structure to meet Pantheon Caching Requirements
   * https://pantheon.io/docs/caching-advanced-topics#using-your-own-session-style-cookies
   */
  const INVITATION_COOKIE_NAME = 'STYXKEY_takedaiduserinvitationinfo';
  const INVITATION_FORM_TYPE_COOKIE_NAME = 'STYXKEY_takedaidformtype';

  /**
   * Supported Countries for Takeda ID
   * Used to filter the Drupal Country Manager list to supported locales
   * See https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Locale!CountryManager.php/9.3.x
   */
  const SUPPORTED_COUNTRIES = [
    'AE', // UAE
    'AL', // Albania
    'AR', // Argentina
    'AT', // Austria
    'AU', // Australia
    'BA', // Bosnia and Herzegovina
    'BE', // Belgium
    'BG', // Bulgaria
    'BR', // Brazil
    'BY', // Belarus
    'CA', // Canada
    'CH', // Switzerland
    'CN', // China
    'CO', // Colombia
    'CZ', // Czech
    'DE', // Germany
    'DK', // Denmark
    'DZ', // Algeria
    'EE', // Estonia
    'ES', // Spain
    'FI', // Finland
    'FR', // France
    'GB', // Great Britain
    'GR', // Greece
    'HR', // Croatia
    'HU', // Hungary
    'ID', // Indonesia
    'IE', // Ireland
    'IL', // Israel
    'IN', // India
    'IT', // Italy
    'KR', // South Korea
    'KZ', // Kazakhstan
    'LT', // Lithuania
    'LV', // Latvia
    'ME', // Montenegro
    'MK', // Macedonia
    'MX', // Mexico
    'MY', // Malaysia
    'NL', // Netherland
    'NO', // Norway
    'NZ', // New Zealand
    'PH', // Philippines
    'PL', // Poland
    'PT', // Portugal
    'RO', // Romania
    'RS', // Serbia
    'RU', // Russia
    'SA', // Saudi Arabia
    'SE', // Sweden
    'SG', // Singapore
    'SI', // Slovenia
    'SK', // Slovakia
    'TH', // Thailand
    'TR', // Turkey
    'TW', // Taiwan
    'UA', // Ukraine
    'VN'  // Vietnam
  ];

}