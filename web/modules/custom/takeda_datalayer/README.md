This module adds support for pushing a logged in user's Takeda Digital ID to Google Tag Manager and Matomo's DataLayer.

## Initial Setup

- This module depends on either the Google Tag Manager (`google_tag`) or Matomo (`matomo`) Drupal modules.
- Enable your Tag Manager module and configure your container by adding the appropriate Container ID configuration

## Google Analytics / Google Tag Manager Configuration

To receive information from the Data Layer module, your Google Analytics property must be configured to accept the digitalId as a custom dimension.

- Within your Google Analytics Property specific to the site, create a HIT scope custom dimension with the name "Takeda Digital ID"
- In Google Tag Manager, create a dataLayer variable "DL - Takeda Digital ID" and pass "digitalID" in the dataLayer variable name field
- After creating the dataLayer variable, edit "Google Analytics Settings" variable and pass {{DL -- Takeda Digital ID}} variable in the custom dimensions with the same index generated in Step 1

## Module Use

- The Takeda DataLayer Module checks for the presence of the `google_tag` and/or `matomo` modules
- When a user is logged in via Takeda ID, their Digital Id will be automatically added to the GTM or Matomo DataLayer on load.