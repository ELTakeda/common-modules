jQuery(document).ready(function () {
    var $calculatorModule = jQuery('.calculators-module');
    var $leavePopup = jQuery('.js-about-to-leave-popup');
    var $popupBtnStay = jQuery('.js-btn-stay');
    var $popupBtnLeave = jQuery('.js-btn-leave');
    var $popupModal = jQuery('.js-popup-modal');
    var $backToTopBtn = jQuery('.js-calculator-back-to-top');
    var $linkElements = jQuery( ".calculators-module .calculator__footer" ).find("a");
    $linkElements.addClass('js-calculator-external-link');
    var $externalLinks = jQuery('.js-calculator-external-link');

    moduleInit();

    //---Functions---

    function moduleInit() {
        attachEventListeners();
    }

    function attachEventListeners() {
        $popupBtnStay.on('click', onStayBtnClick);
        $popupBtnLeave.on('click', onLeaveBtnClick);
        $backToTopBtn.on('click', scrollToTop);
        if ($externalLinks.length > 0) {
            $externalLinks.on('click', externalLinkClickHandler);
        }

    }

    //---Events Handlers---
    function externalLinkClickHandler(event) {
        event.preventDefault();

        var $currentLink = jQuery(this);
        var currentHref = $currentLink.attr('href');
        $leavePopup.attr('data-href', currentHref);
        $leavePopup.addClass('visible');
        $popupModal.addClass('visible');
    }

    function onStayBtnClick(event) {
        $leavePopup.removeClass('visible');
        $popupModal.removeClass('visible');
    }

    function onLeaveBtnClick(event) {
        var currentUrl = $leavePopup.attr('data-href');

        $leavePopup.removeClass('visible');
        $popupModal.removeClass('visible');
        window.open(currentUrl);
    }

    //---Utils---

    function scrollToTop() {
        jQuery([document.documentElement, document.body]).animate({
            scrollTop: $calculatorModule.offset().top
        }, 500);
    }
});
