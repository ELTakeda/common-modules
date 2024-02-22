## Suggested Configuration

- This module requires the Mail System component to be registered and configured to process emails. We recommend using the [SendGrid integration](https://pantheon.io/docs/guides/sendgrid) which integrates with drupal/mailsystem.


## Setup Dependencies

Ensure you have the required depenedcies included within your composer.json

### Mail System

- Enable email modules
- Ensure API keys are set
- Ensure formatter is Swiftmailer

## Takeda ID Field Matching

When creating and matching leads, the Takeda ID module will look for the following fields on your registration form to populate the CRM user if present:

# Key Profile Details
- mail
- field_title
- field_first_name
- field_last_name
- field_customer_id
- field_crm_country

# Other Profile Details
- field_division
- field_department
- field_cost_center
- field_employee_number
- field_mobile_phone
- field_mobile
- field_primary_phone
- field_street_address
- field_city
- field_state
- field_zip_code
- field_post_code

# Lead Details
- field_local_id
- field_primary_place_of_work
- field_address
- field_speciality

Other fields checked as "Pre-fillable Fields" in the Takeda ID Module administration page will be passed to the CRM as "Additional Info".
