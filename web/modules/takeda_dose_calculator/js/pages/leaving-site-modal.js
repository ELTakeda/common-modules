jQuery(document).ready(function () {
    //    --- Elements ---
    var $body = jQuery("body");
    var $aEls = jQuery("a");
    var $leavingSiteModal = jQuery(".js-pr-leaving-site-modal");
    var $modalContent = jQuery(".js-pr-modal-content");
    var $modalConfirmButton = jQuery(".js-pr-confirm-button");
    var $modalCloseButtons = jQuery(".js-pr-close-button");

    // ---  Allowed url ---
    var allowedUrls = $leavingSiteModal.data("allowed_url");
    var allowedUrlsArray = allowedUrls.split(", ");

    // --- Add Event Listeners ---
    $aEls.on("click", linkClickHandler);
    $modalConfirmButton.on("click", openCurrentLink);
    $modalCloseButtons.on("click", closeExternalLinkModal);
    $leavingSiteModal.on("click", closeExternalLinkModal);

    // ---Event Handlers ---
    function linkClickHandler(event) {
        event.preventDefault();
        var currentLink = event.target;
        var currentHost = currentLink.host.trim().replace("www.", "");
        var currentHrefUrl = currentLink.href;

        if (jQuery(currentLink).data("action") === "mailto") {
            window.open(currentHrefUrl);
            return;
        }

        var isAllowedURL = false;
        jQuery.each(allowedUrlsArray, function (index, url) {
            if (url == currentHost) {
                isAllowedURL = true;
                return;
            }
        });

        if (isAllowedURL) {
            window.open(currentHrefUrl, "_self");
        } else {
            $leavingSiteModal.addClass("pr-visible");
            $body.addClass("pr-unscrollable");
            $modalContent.addClass("pr-visible");
            $modalConfirmButton.attr("data-current_url", currentHrefUrl);
        }
    }

    function closeExternalLinkModal(event) {
        $leavingSiteModal.removeClass("pr-visible");
        $modalContent.removeClass("pr-visible");
        $body.removeClass("pr-unscrollable");
    }

    function openCurrentLink(event) {
        var $confirmButton = jQuery(this);
        var currentUrl = $confirmButton.data("current_url");
        window.open(currentUrl);
        closeExternalLinkModal();
    }
});
