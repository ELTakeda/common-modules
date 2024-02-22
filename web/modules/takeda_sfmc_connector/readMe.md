## Setup for changes
1. Navigate in console to this folder
2. Install dependencies - npm install
3. Start watching scss for changes - gulp watch

## Colors
Change theme colors in /scss/shared/_variables.scss where:


- $color-primary: Primary Text Color;
- $color-secondary: Title, button and notification color( at the moment: red);
- $color-secondary--hover: Used on hover on button and links;
- $color-border: Used for borders

## Fonts
Open Sans is the default font. If want to change it add new fonts:
- add new font(woff format) in the assets/fonts folder
- Change font faces in scss/shared/_fonts.scss
- change values in the scss/shared/_variables.scss medium to medium, regular ot regular etc. Example below:

## Width
You can change max width in /scss/shared/_variables.
- $max-module-width: (400 by default);