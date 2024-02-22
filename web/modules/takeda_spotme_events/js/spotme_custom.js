(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.spotMeCustom = {
    attach: function(context) {
      $(window).on('load', function() {
        $('.video-component-popup-page-wrapper .btn-event').on('click mousedown', function () {
          window.location.hash = $(this).attr('data-target').replace('#', '');
        });

        $('.modal').on('hidden.bs.modal', function () {
          $('.modal video').each(function () {
            $(this).trigger('pause');
          });
          $('.modal iframe').each(function () {
            $(this).attr("src", $(this).attr("src"));
          });
        });
        if (window.location.hash && $('.video-component-popup-page-wrapper').length > 0) {
          let hash_split = window.location.hash.split("-");
          if (hash_split[0] == '#video') {
            hash_split.pop();
            hash_split = hash_split.join('-');

            const target = $('.video-component-popup-page-wrapper .btn-event[data-target^="'+hash_split+'-"]');
            if (target.length > 0) {
              let position = target.offset().top - 100;
              if (window.matchMedia('(max-width: 992px)').matches) {
                position = target.offset().top - target.outerHeight();
              }
              $('body,html').animate({scrollTop: position}, 100);

              setTimeout(function (){
                target.trigger('click');
              }, 600);
            }
          }
        }
      });

      

      // Close popup.
      $('.btn-close').on('click', function (e) {
        e.preventDefault();
        $('.ui-dialog-titlebar-close').trigger('click');
      });

      $('.btn-register-event').on('click', function(e) {
console.log('here');
        e.preventDefault();
        let ajaxSettings = {
          url: '/spotme/register-event/' + $(this).attr('event-nid') + '/' +  $(this).attr('webinar-id'),
        };
        let myAjaxObject = Drupal.ajax(ajaxSettings);
        myAjaxObject.execute();
      });

      $(window).on('load', function(){
        $('#myTabContentWebinar .tab-pane').each(function(){
          if ($(this).find('.col-12').length > 0) {
            $(this).find('.view-more-webinar').removeClass('d-none');
          }
        });
      });
      $('.btn-reload-page').on('click', function(e) {
        e.preventDefault();
        location.reload();
      });
    }
  };
  Drupal.AjaxCommands.prototype.ReloadPageCommandSpotme =
    function(ajax, response, status) {
      location.reload();
    };
})(jQuery, Drupal);
