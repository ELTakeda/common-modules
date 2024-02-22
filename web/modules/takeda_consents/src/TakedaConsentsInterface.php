<?php

namespace Drupal\takeda_consents;

/**
 * Provides an interface for Takeda Consents.
 */
interface TakedaConsentsInterface {
  /**
   * Takeda Consents Config Name
   */
  const CONFIG_OBJECT_NAME = 'consents_gem_countries.settings';

  /**
   * Supported GEM Countries for Takeda Consents
   */
  const SUPPORTED_GEM_COUNTRIES = [
    'AE', // UAE
    'AR', // Argentina
    'AU', // Australia
    'BR', // Brazil
    'CN', // China
    'ID', // Indonesia
    'IL', // Israel
    'IN', // India
    'KR', // South Korea
    'KZ', // Kazakhstan
    'MX', // Mexico
    'MY', // Malaysia
    'PH', // Philippines
    'RU', // Russia
    'SA', // Saudi Arabia
    'SG', // Singapore
    'TH', // Thailand
    'TR', // Turkey
    'TW', // Taiwan
    'VN'  // Vietnam
  ];

}