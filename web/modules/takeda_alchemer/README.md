# TAKEDA ALCHEMER MODULE  
The purpose of the module is for agencies and content feeders to be able to provide surveys to HCPs on the websites they access. A tagging system is implemented as part of the whole module, which provides the option to track users' activity on a Business unit. Tags should be editable by website developers, but the functionality to add tags to resources within the website should be available and configurable. Tags should be configured for therapy areas (part of a website that can be accessed from the parent website), resources, products, items, articles, materials, etc.

The module also provides the possibility to include a microfeedback survey, which leverages a thumbs-up/thumbs-down feedback mechanism. The purpose of this is for Marketers to get insight into the appropriateness and suitability of content for further improvements.

## FOR SITE ADMINISTRATORS  
The Site Administrator can install this module and do the following:
 1. From first configuration form (Alchemer basic settings)  
 -  Choose site Enviroment (DEV, UAT, etc)
 -  Add site ID
 -  Add basic microfeedback url for each site
 -  Add allowed list of countries (names and their codes) and choose country from them
 -  Add allowed list of therapy areas and products, sections and functions

 2. From second configuration form (Alchemer popup tags settings):
 - Add multiple groups. Each group contains 5 text plain fields for therapy areas and products and a dropdown list containing pages on which we can apply these tags to. A single page can participate in only one group, so the maximum number of groups that can be added is limited.
 - Remove created groups

 3. From third configuration form (Alchemer microfeedback settings)
 - Add multiple groups. Each group contains 3 text plain fields for therapy areas and products and a dropdown list containing pages on which we can apply these tags to. A single page can participate in only one group, so the maximum number of groups that can be added is limited.
 - Remove created groups
 - Add existing class attribute of a html container in which we want to place the microfeedback block

 4. From specific content page edit:
 - Turn on/off alchemer functionality (Turn on Alchemer Microfeedback and Alchemer Popup for this page)
 - Choose which tags to include in serveys (Country, Website Name, Therapy Area,
   Product of the website, HCP ID, URL, Section, Function).
 - Add or edit therapy areas if "Therapy Area" tag is ticked.
 - Add or edit products if "Product of the website" tag is ticked.
 - Add or edit function if "Function" tag is ticked.
 - Add or edit section if "Section" tag is ticked.

 5. From Block Layout - admin/structure/block
  - Choose which region to place Microfeedback iframe block
  - Manage Microfeedback Iframe Block visibility. It can be configured to show only on specific content types, languages, roles, etc.
 


## FOR SITE DEVELOPERS  
There is currently no information available that may be useful for site developers.


## FOR MODULE MAINTAINERS  
The module has 6 key parts:
1. .module file - Contains 2 main hook functions:
    - takeda_alchemer_page_bottom - Adds a script to the bottom of the page that contains the alchemer tags to be passed to the survey via beacons. 
    // the information in the script tag looks like this
    (function(d,e,j,h,f,c,b){d.SurveyGizmoBeacon=f;d[f]=d[f]||function(){(d[f].q=d[f].q||[]).push(arguments)};c=e.createElement(j),b=e.getElementsByTagName(j)[0];c.async=1;c.src=h;b.parentNode.insertBefore(c,b)})(window,document,'script','sg_beacon');

    - takeda_alchemer_form_node_form_alter - Adds 2 separate sections for popup and microfeedback surveys
      with the necessary tags for each node edit page, modifying the existing form.

2. .install file - This file add two new tables to the DB:
    takeda_alchemer_popup - stores tags and data for alchemer popup survey
    takeda_alchemer_microfeedback - stores tags and data for alchemer microfeedback survey

3. Controller for the autocomplete (TakedaAlchemerAutocomplete) - Returns a JSON Response. As а result    creates a suggestion dropdown of tags that contain the text entered in the field.

4. Configuration forms. This module contains three configuration forms
   4.1.  TakedaAlchemerConfigureForm - The form defines the fields for the basic alchemer configuration 
   that the Site Administrator can enter. 

   4.2.  TakedaAlchemerPopup - This form defines groups that the site administrator can add and delete.
   Each group contains text plain fields for therapy area, products of the website and function .

   4.3.  TakedaAlchemerMicrofeedback - This form defines:
    -  groups that the site administrator can add and delete.
    Each group contains text plain fields for therapy area, products of the website, function and section.
    -  text plain field for existing class attribute of a html container.

5. Block plugin for the microfeedback iframe thumbs (MicrofeedbackIframeBlock) - Contains build() function
   that defines a twig template for the iframe and passes the tag data to it. ? 

6. Service classes (Helpers) - Helper, TagsConfigHelper and FormBuilder
Represent classes that were created to improve code readability and reduce code duplication in module core files. Contains functions that are reusable.


## REQUIRED MODULES  
This module depends on the following modules:
    1. takeda_id - The module needs to be configured with TakedaID


## EXTENSION MODULES  
Currently - none. 

## TO DO  
There are currently no known issues.  


## KNOWN ISSUES  
There are currently no known issues.  