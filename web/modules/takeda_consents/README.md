# TAKEDA CONSENTS MODULE  

The Takeda Consents module allows adding and configuring consents for the current site.  
After the purposes, preferences and options are defined, the consents are displayed on the default user registration form ( /user/register ) and when that form is submitted, the data is sent to the configured data ( /admin/config/people/takeda_consents_purposes ). The data is sent to OneTrust with the input URL and credentials.


## PREREQUISITES
The default form must contain the following fields and their respective name attributes:  
- Email (mail)  
- First Name (field_first_name)  
- Last Name (field_last_name)  
- CRM Country (field_crm_country)  


## FOR SITE ADMINISTRATORS  
The Site Administrator can install this module and do the following:  
- Add purposes - the URL they have to be send to, their API Key and Secret, the Request Info parameter (all received from a request to the OneTrust support). The Site Administrator can also add the Purpose ID which is sent to OneTrust and a Purpose Name to be used on the site. There is also a checkbox to enable preferences and options for the prupose which can be configured in a separate tab.  
This functionality is accessed at /admin/config/people/takeda_consents_purposes  
- Add preferences and options - the Site Administrator can click the checkbox to activate the preferences and options configuration in the described previous step. Then the user can add multiple preferences and their respective multiple options, which both have fields for ID numbers which are used in the request to OneTrust and names to be displayed on the site  
This functionality can be accessed at /admin/config/people/takeda_consents_preferences_and_options  
- Automatically display the purposes (if no options are input) at the bottom of the registration form ( /user/register )  
- Automatically display the options (if they are input) at the bottom of the registration form ( /user/register )  


## FOR SITE DEVELOPERS  
The Site Developer can use the data from the module in the following ways:  
1. With the variable takeda_consents_values which is accessible on every node  
2. By copying the structure of the fields on the /user/register page  
3. With the following code in his custom hook / controller / function  
    // Get the saved data of the config
    $config = \Drupal::config('takeda_consents.settings');
    $takeda_consents_values = $config->get('takeda_consents_values');


## FOR MODULE MAINTAINERS  
The module has three key parts:  
1. The Purpose Configuration Form - the form defines the fields that the Site Administrator can enter and how he can add or remove fields.  
2. The Preferences and Options Configuration Form - the form displays a list of all Purposes that have the Preferences and Options checkbox enabled. The form defines the fields that the Site Administrator can enter and how he can add or remove fields. 
3. The .module file - the file adds the saved config data to a variable that is accessible by the Site Developer on all nodes. The module also renders all Purposes or Options of Purposes (where present) on the default User Registration page (/user/register)  
4. You can run the TakedaConsentsTests.php file to execute various predefined tests or add new relevant tests to it.  


## REQUIRED MODULES  
- takeda_id


## EXTENSION MODULES  
- takeda_consents_language - the module uses the data created by this module on a site that has the Drupal Language related modules installed and allows the data to be toggled and translated for each language code present on the site.  


## TO DO  
There are currently no TO DOs.


## KNOWN ISSUES  
There are currently no known issues.  
