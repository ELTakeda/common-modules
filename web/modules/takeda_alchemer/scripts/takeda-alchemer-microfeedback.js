 jQuery(document).ready(function () {
    // --- Elements --
    var $iframeContainer = jQuery(".js-microfeedback-iframe-container");

    // --- Get Data ---
    var customContainerClass = $iframeContainer.attr("data-iframe-class");

    if (customContainerClass.length > 0) {
        placeIframeInCustomContainer(customContainerClass);
    } else {
        $iframeContainer.removeClass("tak-hidden");
    }


    // ---Functions ---
    function placeIframeInCustomContainer(customContainerClass) {
        var $customIframeContainer = jQuery("." + customContainerClass);
        var $iframeCopy = $iframeContainer.clone();

        $iframeContainer.remove();
        $customIframeContainer.append($iframeCopy);
        $iframeCopy.removeClass("tak-hidden");
    }

});