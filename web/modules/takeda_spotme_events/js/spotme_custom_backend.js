(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.spotMeCustomBackend = {
    attach: function(context) {
      if ($('form[id^=views-exposed-form-webinar-export-page-1]').length > 0) {
        $('.views-data-export-feed a').appendTo($('form[id^=views-exposed-form-webinar-export-page-1] div[id^=edit-actions]'))
        $('form[id^=views-exposed-form-webinar-export-page-1]');
        $( "<span>~ </span>").insertBefore($("form[id^=views-exposed-form-webinar-export-page-1] input[name='field_start_at_value[max]']"));
      }
    }
  };
})(jQuery, Drupal);
