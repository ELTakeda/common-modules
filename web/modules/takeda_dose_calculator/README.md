Introduction:

The purpose of this module is to provide HCPs the ability to calculate the amount of several medical drugs to be provided to a patient. Currently, there are three calculators, which will be made.

Type of calculators:
 - Ninlaro Calculator:
  Each part of the calculator contains several questions. After answering every question on the section, next section will be unlocked.

    Upon completion of all  all three pages(sections), there is a input for the age - needs to be filled. There is also a button for calculating the overall results, which are then displayed.

    Upon installation, the module will create a new page on the website. In the edit of the page there are textfields which will determine:
        - result button text (the text within the button which will be dispalyed in the form)
        - the reference links in the footer
      
 - Advante Calculator:
    The calculator displays a table with information, which needs to be filled from the CMS and is editable.
    At the bottom of the table there are three fields that need to be populated by the HCP, which will lead to calculation of the dose to be prescribed to the patient.  There are requirements for the values (validations for max numbers to be added in each of the three boxes).

    There is also a button for “Calculate” and another one for “Reset Calculation”. The third button is for exiting the website - upon click, a pop-up appears with a message that the HCP is about to leave the website (two options - Yes and Cancel).

    Upon installation, the module will create a new page on the website. In the edit of the page there are textfields which will determine:
        - calculate button text
        - reset button text
        - reference links
        - Recommendations table filling
- Feiba calculator:
    The Febia calculator is similar to the Advante in terms of structure and functionality. There is also a table with information, but has only two fields with information to be added by the HCP for the calculation to happen. There is also a “Calculate” and a ”Reset Calculation” button.

    A text field with references is also present and should be editable from the CMS.

    The third button is for exiting the website - upon click, a pop-up appears with a message that the HCP is about to leave the website (two options - Yes and Cancel).

    Upon installation, the module will create a new page on the website. In the edit of the page there are textfields which will determine:
        - calculate button text
        - reset button text
        - title color
        - reference links
        - Recommendations table filling

The editors can configure which calculator to be displayed in the website when they  go in the edit of the corresponding calculator and in the field menu settings click the checkbox provide a menu link.

Note: There should not be included any other elements in the calculators pages as the style may break.

Style changes:
- Open terminal and navigate to the takeda_dose_calculator folder
- Install dependencies - npm install
- Start Gulp for scsss => css compilation with command "gulp watch"

There is several types of calculator with separate styles:
- Ninlaro
-Adcedtris
- Vpriv
- Type a calcualtor:   includes :
    - Advante
    - Feiba
    - Prothromplex
    - Adynovate

Every type have separate scss folder. In shared/varriables you can change colors and fonts.
IMPORTANT: Don't change variables names only values!

If you want to change fonts you must add new fonts to the modules assests in fonts folder, modify font faces in calculator's /shared/_fonts.scss
file and add new value to the variable in /shared/_variables.scss
    
     