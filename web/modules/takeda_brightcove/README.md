This module adds support for loading Brightcove Video, Audio, and Gallery/Experience embeds as Media Types within Drupal, allowing them to be used wherever you would normally add media items.

## Suggested Configuration

- This module requires the default Media module to be installed / enabled. 

- To support adding media via the CKEditor, you'll also need to enable the Media Library module.

## Initial Setup

- Add your Brightcove information to the configuration to support loading thumbnails and content information

- Add your desired Media Types within the Structure -> Media Types configuration
- Update the display mode for your new media types (under Edit -> Manage Display) to use the appropriate Brightcove Player format


## Rich Text Editor Support

Your media types can be enabled for embedding within rich text editors.

- Configure the text editor used within your pages under Configure -> Text formats and editors
- Drag the "Insert from Media Library" toolbar item to your CKEditor toolbar
- Ensure the "Embed Media" filter is checked
- Under the "Embed Media" filter settings, select the media types you'd like editors to select from

## Content Field Support

Your media types can be added as fields within any of your Content Types.

- Add or configure your content type under Structure -> Content Types
- On the Manage Field page, add a new "Media" field type and supply a label, and configure your limits
- On the Edit Field page, check the appropriate Media type under the "Reference Type" section


## Adding Media

Add media within the Media Library picker by pasting the Brightcove Studio URL

- The module will automatically pull in the thumbnail and title where available
- Your configuration, including text tracks and captions, will be automatically loaded when displaying your embed.