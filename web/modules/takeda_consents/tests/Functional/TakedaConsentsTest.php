<?php

namespace Drupal\Tests\takeda_consents\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests for the Lorem Ipsum module.
 *
 * @group takeda_consents
 */
class TakedaConsentsTest extends BrowserTestBase {
    /**
     * Modules to install.
     *
     * @var array
     */
    protected static $modules = ['takeda_consents'];

    /**
     * Setting the default theme due to it being required.
     * 
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    /**
     * A simple user.
     *
     * @var \Drupal\user\Entity\User
     */
    private $user;

    /**
     * Perform initial setup tasks that run before every test method.
     */
    public function setUp(): void {
        parent::setUp();
        $this->user = $this->drupalCreateUser([
            'administer site configuration'
        ]);
    }

    /**
     * A function used to veriy if a field value exists.
     * Accepts an array of field names and value pairs.
     */
    public function verifyFieldValues($field_value_array) {
        foreach ($field_value_array as $field_name => $field_value) {
            $this->assertSession()->fieldValueEquals(
                $field_name,
                $field_value
            );
        }
    }

    /**
     * Tests the purpose config form.
     * 
     * Test the initial fields.
     * Test the form submit.
     * Test adding a field and saving.
     * Test adding multiple fields, altering the first and saving.
     * Test removing the middle field from three fields and reindexing the array.
     * 
     */
    public function testPurposeConfigForm() {
        // Login.
        $this->drupalLogin($this->user);

        // Test access to config page.
        $this->drupalGet('admin/config/people/takeda_consents_purposes');
        $this->assertSession()->statusCodeEquals(200);

        // Reusable variables for the static field IDs.
        $omnichannel_url = 'edit-takeda-consents-fieldset-omnichannel-url';
        $omnichannel_key = 'edit-takeda-consents-fieldset-omnichannel-api-key';
        $omnichannel_secret = 'edit-takeda-consents-fieldset-omnichannel-api-secret';

        // Test if the form includes the static fields required for all purposes. (by ID)
        $this->assertSession()->fieldExists($omnichannel_url);
        $this->assertSession()->fieldExists($omnichannel_key);
        $this->assertSession()->fieldExists($omnichannel_secret);

        // Fill the static fields.
        $static_field_data = [
            $omnichannel_url => 'omnichannel_url',
            $omnichannel_key => 'omnichannel_key',
            $omnichannel_secret => 'omnichannel_secret'
        ];

        // Submit the form. (form data and button id)
        $this->submitForm($static_field_data, 'edit-actions-submit');

        // Go to the page again to verify the saved values.
        $this->drupalGet('admin/config/people/takeda_consents_purposes');
        $this->assertSession()->statusCodeEquals(200);

        // Test the fields of the page for the saved form data values.
        $this->verifyFieldValues($static_field_data);

        // Confirm that there are no purposes. (by CSS selector)
        $this->assertSession()->elementNotExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-0');

        /*
         * FIRST PART
         * Adding a purpose and saving it
         * 
        */
        // Test the add purpose button. (by CSS selector)
        $this->click('#edit-takeda-consents-fieldset-actions-add-fieldset');

        // Confirm that there is a purpose div. (by CSS selector)
        $this->assertSession()->elementExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-0');

        // Reusable variables for the field IDs.
        $request_info_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-request-info';
        $request_info_double_optin_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-request-info-second';
        $purpose_id_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-purpose-id';
        $purpose_name_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-purpose-name';
        $purpose_identifier_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-purpose-identifier';
        $purpose_preference_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-purpose-preference-boolean';

        // Confirm that all fields exist for the first purpose. (by ID)
        $this->assertSession()->fieldExists($request_info_0);
        $this->assertSession()->fieldExists($request_info_double_optin_0);
        $this->assertSession()->fieldExists($purpose_id_0);
        $this->assertSession()->fieldExists($purpose_name_0);
        $this->assertSession()->fieldExists($purpose_identifier_0);
        $this->assertSession()->fieldExists($purpose_preference_0);

        // Fill the fields.
        $form_data = [
            $request_info_0 => 'Request Info Value',
            $request_info_double_optin_0 => 'Request Info Double Optin Value',
            $purpose_id_0 => 'purpose-id-value-test-0', // Must contain numbers, letters and dashes only.
            $purpose_name_0 => 'Purpose Name Value',
            $purpose_identifier_0 => 'email',
            $purpose_preference_0 => TRUE, // TRUE or FALSE
        ];

        // Submit the form. (form data and button id)
        $this->submitForm($form_data, 'edit-actions-submit');

        // Go to the page again to verify the saved values.
        $this->drupalGet('admin/config/people/takeda_consents_purposes');
        $this->assertSession()->statusCodeEquals(200);

        // Test the fields of the page for the saved form data values.
        $this->verifyFieldValues($form_data);

        /*
         * SECOND PART
         * Adding multiple purposes and saving them
         * 
        */
        // Add two purposes. (by CSS selector)
        $this->click('#edit-takeda-consents-fieldset-actions-add-fieldset');
        $this->click('#edit-takeda-consents-fieldset-actions-add-fieldset');

        // Verify that the three expected DIVs exist.
        $this->assertSession()->elementExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-0');
        $this->assertSession()->elementExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-1');
        $this->assertSession()->elementExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-2');

        // Reusable variables for the field IDs.
        $request_info_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-request-info';
        $request_info_double_optin_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-request-info-second';
        $purpose_id_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-purpose-id';
        $purpose_name_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-purpose-name';
        $purpose_identifier_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-purpose-identifier';
        $purpose_preference_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-purpose-preference-boolean';
        $request_info_2 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-2-request-info';
        $request_info_double_optin_2 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-2-request-info-second';
        $purpose_id_2 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-2-purpose-id';
        $purpose_name_2 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-2-purpose-name';
        $purpose_identifier_2 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-2-purpose-identifier';
        $purpose_preference_2 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-2-purpose-preference-boolean';

        // Fill the fields.
        $form_data_three_purposes = [
            $request_info_0 => 'Request Info Value Edited',
            $request_info_double_optin_0 => 'Request Info Double Optin Value Edited',
            $purpose_id_0 => 'purpose-id-value-test-0', // Must contain numbers, letters and dashes only.
            $purpose_name_0 => 'Purpose Name Value Edited',
            $purpose_identifier_0 => 'email',
            $purpose_preference_0 => TRUE, // TRUE or FALSE

            $request_info_1 => 'Request Info Value 1',
            $purpose_id_1 => 'purpose-id-value-test-1', // Must contain numbers, letters and dashes only.
            $purpose_name_1 => 'Purpose Name Value 1',
            $purpose_identifier_1 => 'takedaid',
            $purpose_preference_1 => TRUE, // TRUE or FALSE

            $request_info_2 => 'Request Info Value 2',
            $request_info_double_optin_2 => 'Request Info Double Optin Value Value 2',
            $purpose_id_2 => 'purpose-id-value-test-2', // Must contain numbers, letters and dashes only.
            $purpose_name_2 => 'Purpose Name Value 2',
            $purpose_identifier_2 => 'email',
            $purpose_preference_2 => FALSE, // TRUE or FALSE
        ];

        // Submit the form. (form data and button id)
        $this->submitForm($form_data_three_purposes, 'edit-actions-submit');

        // Go to the page again to verify the saved values.
        $this->drupalGet('admin/config/people/takeda_consents_purposes');
        $this->assertSession()->statusCodeEquals(200);

        // Test the fields of the page for the saved form data values.
        $this->verifyFieldValues($form_data_three_purposes);

        /*
         * THIRD PART
         * Removing a purpose and saving the rest
         * 
        */
        // Click the remove purpose button. (by CSS selector)
        $this->click('[name="button_remove_1"]');
        
        // Verify that the first and third remain and that the second was removed.
        $this->assertSession()->elementExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-0');
        $this->assertSession()->elementNotExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-1');
        $this->assertSession()->elementExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-2');

        // Submit the form. (form data and button id)
        $this->submitForm([], 'edit-actions-submit');

        // Go to the page again to verify the saved values.
        $this->drupalGet('admin/config/people/takeda_consents_purposes');
        $this->assertSession()->statusCodeEquals(200);

        // Verify that the first and third remain and that the second was removed.
        // After the purpose removal, the fields should be reindexed.
        // Meaning that all _2 fields become _1 fields.
        $this->assertSession()->elementExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-0');
        $this->assertSession()->elementExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-1');
        $this->assertSession()->elementNotExists('css', '#edit-takeda-consents-fieldset-takeda-consents-purposes-2');

        // Test the fields of the page for the saved form data values.
        $form_data_remove_test = [
            $request_info_0 => 'Request Info Value Edited',
            $request_info_double_optin_0 => 'Request Info Double Optin Value Edited',
            $purpose_id_0 => 'purpose-id-value-test-0',
            $purpose_name_0 => 'Purpose Name Value Edited',
            $purpose_identifier_0 => 'email',
            $purpose_preference_0 => TRUE,

            $request_info_1 => 'Request Info Value 2',
            $request_info_double_optin_1 => 'Request Info Double Optin Value Value 2',
            $purpose_id_1 => 'purpose-id-value-test-2',
            $purpose_name_1 => 'Purpose Name Value 2',
            $purpose_identifier_1 => 'email',
            $purpose_preference_1 => FALSE,
        ];

        $this->verifyFieldValues($form_data_remove_test);
    }

    /**
     * Tests the purpose config form.
     */
    public function testPreferenceAndOptionsConfigForm() {
        // Initial setup
        // Login.
        $this->drupalLogin($this->user);

        // Test access to config page.
        $this->drupalGet('admin/config/people/takeda_consents_purposes');
        $this->assertSession()->statusCodeEquals(200);

        // Reusable variables for the static field IDs.
        $omnichannel_url = 'edit-takeda-consents-fieldset-omnichannel-url';
        $omnichannel_key = 'edit-takeda-consents-fieldset-omnichannel-api-key';
        $omnichannel_secret = 'edit-takeda-consents-fieldset-omnichannel-api-secret';

        // Add two purposes. (by CSS selector)
        $this->click('#edit-takeda-consents-fieldset-actions-add-fieldset');
        $this->click('#edit-takeda-consents-fieldset-actions-add-fieldset');

        // Reusable variables for the field IDs.
        $request_info_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-request-info';
        $request_info_double_optin_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-request-info-second';
        $purpose_id_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-purpose-id';
        $purpose_name_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-purpose-name';
        $purpose_identifier_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-purpose-identifier';
        $purpose_preference_0 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-0-purpose-preference-boolean';

        $request_info_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-request-info';
        $request_info_double_optin_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-request-info-second';
        $purpose_id_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-purpose-id';
        $purpose_name_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-purpose-name';
        $purpose_identifier_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-purpose-identifier';
        $purpose_preference_1 = 'edit-takeda-consents-fieldset-takeda-consents-purposes-1-purpose-preference-boolean';

        // Fill the fields.
        $form_data = [
            $omnichannel_url => 'omnichannel_url',
            $omnichannel_key => 'omnichannel_key',
            $omnichannel_secret => 'omnichannel_secret',

            $request_info_0 => 'Request Info Value Edited',
            $request_info_double_optin_0 => 'Request Info Double Optin Value Edited',
            $purpose_id_0 => 'purpose-id-value-test-0', // Must contain numbers, letters and dashes only.
            $purpose_name_0 => 'Purpose Name Value Edited',
            $purpose_identifier_0 => 'email',
            $purpose_preference_0 => TRUE, // TRUE or FALSE

            $request_info_1 => 'Request Info Value 1',
            $purpose_id_1 => 'purpose-id-value-test-1', // Must contain numbers, letters and dashes only.
            $purpose_name_1 => 'Purpose Name Value 1',
            $purpose_identifier_1 => 'takedaid',
            $purpose_preference_1 => FALSE, // TRUE or FALSE
        ];

        // Submit the form. (form data and button id)
        $this->submitForm($form_data, 'edit-actions-submit');

        // Go to the page again to verify the saved values.
        $this->drupalGet('admin/config/people/takeda_consents_purposes');
        $this->assertSession()->statusCodeEquals(200);

        // Test the fields of the page for the saved form data values.
        $this->verifyFieldValues($form_data);

        // Tests after initial setup.
        /*
         * FIRST PART
         * Adding the initial preference with one automatically added option
         * 
        */
        // Go to the preferences and options page.
        $this->drupalGet('admin/config/people/takeda_consents_preferences_and_options');
        $this->assertSession()->statusCodeEquals(200);

        // Reusable select id.
        $purpose_select = 'edit-purpose-select';

        // Verify that the select exists.
        $this->assertSession()->selectExists($purpose_select);

        // Verify that the select is not selected.
        $selectValue = [
            $purpose_select => ''
        ];

        $this->verifyFieldValues($selectValue);

        // Verify that there is only one option in the dropdown (+1 for the default empty).
        $purpose_select_options = $this->getOptions($purpose_select);
        $this->assertEquals(2, count($purpose_select_options));

        // Verify that there is only one main container with settings.
        $this->assertSession()->elementExists('css', '#takeda-consents-individual-fieldset-wrapper-0');
        $this->assertSession()->elementNotExists('css', '#takeda-consents-individual-fieldset-wrapper-1');

        // Verify that the main container is hidden.
        // Reason for being commented out - cannot seem to get the style attribute to verify the hidden property.
        // $this->assertSession()->elementAttributeContains('css', '#edit-takeda-consents-fieldset-0-preferences-and-options', 'style', 'display: none;');

        // Verify that the preference container exists. And the individual preference container does not.
        $this->assertSession()->elementExists('css', '#takeda-consents-individual-fieldset-wrapper-0');
        $this->assertSession()->elementExists('css', '#edit-takeda-consents-fieldset-0-preferences-and-options');
        $this->assertSession()->elementNotExists('css', '#takeda-consents-individual-preference-fieldset-wrapper-0');

        // Add a preference. (by CSS selector)
        $this->click('#preferences_add_0');

        // Verify that a preference was added.
        $this->assertSession()->elementExists('css', '#takeda-consents-individual-preference-fieldset-wrapper-0');

        // Reusable variables for the preference and optionfield names.
        $preference_id_0 = 'takeda_consents_fieldset[0][preferences_and_options][0][preference_fieldset][preference_id]';
        $preference_name_0 = 'takeda_consents_fieldset[0][preferences_and_options][0][preference_fieldset][preference_name]';
        $preference_form_visibility_0 = 'takeda_consents_fieldset[0][preferences_and_options][0][preference_fieldset][preference_hidden_boolean]';
        $preference_option_id_0_0 = 'takeda_consents_fieldset[0][preferences_and_options][0][preference_fieldset][preference_options][0][option_fieldset][option_id]';
        $preference_option_name_0_0 = 'takeda_consents_fieldset[0][preferences_and_options][0][preference_fieldset][preference_options][0][option_fieldset][option_name]';
        $preference_option_weight_0_0 = 'takeda_consents_fieldset[0][preferences_and_options][0][preference_fieldset][preference_options][0][option_fieldset][option_weight]';

        // Test if the form includes the static fields required for all preferences. (by ID)
        $this->assertSession()->fieldExists($preference_id_0);
        $this->assertSession()->fieldExists($preference_name_0);
        $this->assertSession()->fieldExists($preference_form_visibility_0);

        // Check if the option value is empty.
        $this->verifyFieldValues([$preference_option_id_0_0 => '']);

        // Fill the static fields.
        $initial_preference_field_data = [
            $preference_id_0 => 'preference-id-value-test-0', // Must contain numbers, letters and dashes only.
            $preference_name_0 => 'Preference 0 Name Value',
            $preference_form_visibility_0 => TRUE, // TRUE or FALSE

            $preference_option_id_0_0 => 'option-id-value-test-0',
            $preference_option_name_0_0 => 'Option 0 Name Value',
            $preference_option_weight_0_0 => 'Option 0 Weight Value'
        ];

        // Verify that there is one option on preference add.
        $this->assertSession()->elementExists('css', '#takeda-consents-individual-options-fieldset-wrapper-0');
        $this->assertSession()->elementNotExists('css', '#takeda-consents-individual-options-fieldset-wrapper-1');

        // Submit the form. (form data and button id)
        $this->submitForm($initial_preference_field_data, 'edit-actions-submit');

        // Go to the page again to verify the saved values.
        $this->drupalGet('admin/config/people/takeda_consents_preferences_and_options');
        $this->assertSession()->statusCodeEquals(200);

        // Update the expected option id.
        $preference_option_id_0_0_new = 'takeda_consents_fieldset[0][preferences_and_options][0][preference_fieldset][preference_options][option-id-value-test-0][option_fieldset][option_id]';
        $preference_option_name_0_0_new = 'takeda_consents_fieldset[0][preferences_and_options][0][preference_fieldset][preference_options][option-id-value-test-0][option_fieldset][option_name]';
        $preference_option_weight_0_0_new = 'takeda_consents_fieldset[0][preferences_and_options][0][preference_fieldset][preference_options][option-id-value-test-0][option_fieldset][option_weight]';

        $saved_preference_field_data = [
            $preference_id_0 => 'preference-id-value-test-0', // Must contain numbers, letters and dashes only.
            $preference_name_0 => 'Preference 0 Name Value',
            $preference_form_visibility_0 => TRUE, // TRUE or FALSE

            $preference_option_id_0_0_new => 'option-id-value-test-0',
            $preference_option_name_0_0_new => 'Option 0 Name Value',
            $preference_option_weight_0_0_new => 'Option 0 Weight Value'
        ];

        // Test the fields of the page for the saved form data values.
        $this->verifyFieldValues($saved_preference_field_data);

        // Test that there aren't more than one preference and one option.
        $this->assertSession()->elementExists('css', '#takeda-consents-individual-preference-fieldset-wrapper-0');
        $this->assertSession()->elementNotExists('css', '#takeda-consents-individual-preference-fieldset-wrapper-1');
        $this->assertSession()->elementExists('css', '#takeda-consents-individual-options-fieldset-wrapper-option-id-value-test-0');
        $this->assertSession()->elementNotExists('css', '#takeda-consents-individual-options-fieldset-wrapper-1');

        // The option should receive the input ID as it's ID, not the default ID received on creation (0, 1, 2).
        $this->assertSession()->elementNotExists('css', '[name="' . $preference_option_id_0_0 . '"]');
        $this->assertSession()->elementExists('css', '[name="' . $preference_option_id_0_0_new . '"]');

        /*
         * SECOND PART
         * Adding more preferences and options to the purpose
         * 
        */
        // TO DO

        /*
         * THIRD PART
         * Removing preferences and options from the purpose
         * 
        */
        // TO DO
    }
}
