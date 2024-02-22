jQuery(document).ready(function () {
    //---Elements---
    var $downloadButton = jQuery('.js-download-button');
    var $allEventsContainer = jQuery('.js-all-events');
    var $foundEvens = jQuery('.js-events-found');
    var $currentPageSpan = jQuery('.js-current-page');
    var $totalPagesSpan = jQuery('.js-total-pages');
    var $changePageButtons = jQuery('.js-page-btn');
    var $eventCtaButtons = jQuery('.js-register');
    var $selectEventType = jQuery('.js-select-event-type');
    var $selectEventLocation = jQuery('.js-select-event-location');
    var $allCards = jQuery('.event-card');
    var $myEventsFilterCheckbox = jQuery('.js-checkbox-container');

    //---Initial settings---
    var cardsPerPage = getCardsPerPageCount();
    var startIndex = 0;
    var currentPage = 1;
    var $cardsToShow = $allCards.slice();
    var cardsToShowCount = $cardsToShow.length;
    var pagesCount = getPagesCount(cardsToShowCount, cardsPerPage);


    initPage();


    //---Functions--
    function initPage() {
        initPagination($cardsToShow, cardsPerPage, startIndex);
        attachEventListeners();
    }

    function initPagination($cardsToShow, cardsPerPage, startIndex) {
        var endIndex = Number(startIndex + cardsPerPage);

        jQuery($cardsToShow).each(function (index, card) {

            if (index >= startIndex && index < endIndex) {
                jQuery(card).removeClass('hidden');
            } else {
                jQuery(card).addClass('hidden');
            }
        });

        $foundEvens.text(cardsToShowCount);
        $totalPagesSpan.text(pagesCount);
        $currentPageSpan.text(currentPage);
    }

    //---Event Handlers---

    function attachEventListeners() {
        $changePageButtons.on('click', pageBtnClickHandler);
        $selectEventType.on('change', eventsFilterHandler);
        $selectEventLocation.on('change', eventsFilterHandler);
        $eventCtaButtons.on('click', eventCtaBtnClickHandler);
        $myEventsFilterCheckbox.on('click', checkboxClickHandler);
    }

    function checkboxClickHandler(event) {
        $myEventsFilterCheckbox.toggleClass('checked');
        eventsFilterHandler();
    }

    function pageBtnClickHandler(event) {
        var btnType = jQuery(this).attr('data-type');

        if (btnType === 'prev') {
            if (currentPage === 1) {
                return;
            }
            startIndex -= cardsPerPage;
            currentPage--;

        } else if (btnType === 'next') {
            if (currentPage === pagesCount) {
                return;
            }
            startIndex += cardsPerPage;
            currentPage++;
        }

        initPagination($cardsToShow, cardsPerPage, startIndex);
        scrollToTop();
    }

    function eventsFilterHandler(event) {
        var selectedEventType = jQuery($selectEventType).val();
        var selectedEventLocation = jQuery($selectEventLocation).val();

        var $filteredByType = $allCards.filter(function (index, currentEvent) {
            var currentEventType = jQuery(currentEvent).attr('data-type');

            if (currentEventType === selectedEventType || selectedEventType === 'all') {
                return currentEvent;
            }
        });

        var $filtered = jQuery($filteredByType).filter(function (index, currentEvent) {
            var currentEventLocation = jQuery(currentEvent).attr('data-location');

            if (currentEventLocation === selectedEventLocation || selectedEventLocation === 'all') {
                return currentEvent;
            }
        });

        if ($myEventsFilterCheckbox.hasClass('checked')) {
            $filtered = $filtered.filter(function (index, currentEvent) {
                var isMyEvent = jQuery(currentEvent).attr('data-isMyEvent').toLowerCase();

                if (isMyEvent === 'yes') {
                    return currentEvent;
                }
            })
        }

        //update information
        jQuery($cardsToShow).addClass('hidden');
        $cardsToShow = $filtered.slice();
        cardsToShowCount = $cardsToShow.length;
        pagesCount = getPagesCount(cardsToShowCount, cardsPerPage);
        startIndex = 0;
        currentPage = 1;

        initPagination($cardsToShow, cardsPerPage, startIndex);
    }

    function eventCtaBtnClickHandler(event) {
        event.preventDefault();

        var $currentBtn = jQuery(this);
        var $currentBtnWrapper = $currentBtn.parent('.js-ctaBtn-wrapper');

        if ($currentBtnWrapper.hasClass('disabled')) {
            return;
        }

        var btnType = $currentBtn.attr('data-btnType');

        switch (btnType) {
            case 'spotme': sendRegRequest($currentBtn, btnType, $currentBtnWrapper); break;
            case 'teams': sendRegRequest($currentBtn, btnType, $currentBtnWrapper); break;
            case 'link': sendRegRequest($currentBtn, btnType, $currentBtnWrapper); break;
            case 'live': openExternalLink($currentBtn); break;
            case 'guestUser': openExternalLink($currentBtn); break;
            default: return;
        }
    }

    //---CTA Buttons specific---

    function openExternalLink($currentBtn) {
        var currentUrl = $currentBtn.attr('href');
        window.open(currentUrl, '_blank');
    }

    function sendRegRequest($currentBtn, btnType, $currentBtnWrapper) {
        var btnRegisteredText = $currentBtn.attr('data-btnRegisteredText');
        var eventId = $currentBtn.attr('data-eventId');
        var spotmeUrl = window.location.origin + '/register-user-for-event-callback';
        var teamsUrl = window.location.origin + '/register-user-for-event-callback';
        var externalLinkUrl = window.location.origin + '/register-user-for-event-callback';
        var requestUrl = '';

        $currentBtn.html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stopwatch" viewBox="0 0 16 16"><path d="M8.5 5.6a.5.5 0 1 0-1 0v2.9h-3a.5.5 0 0 0 0 1H8a.5.5 0 0 0 .5-.5V5.6z"></path><path d="M6.5 1A.5.5 0 0 1 7 .5h2a.5.5 0 0 1 0 1v.57c1.36.196 2.594.78 3.584 1.64a.715.715 0 0 1 .012-.013l.354-.354-.354-.353a.5.5 0 0 1 .707-.708l1.414 1.415a.5.5 0 1 1-.707.707l-.353-.354-.354.354a.512.512 0 0 1-.013.012A7 7 0 1 1 7 2.071V1.5a.5.5 0 0 1-.5-.5zM8 3a6 6 0 1 0 .001 12A6 6 0 0 0 8 3z"></path></svg>');

        switch (btnType) {
            case 'spotme': requestUrl = spotmeUrl; break;
            case 'teams': requestUrl = teamsUrl; break;
            case 'link': requestUrl = externalLinkUrl; break;
        }

        var reqOptions = {
            url: requestUrl,
            type: 'post',
            data: { eventId: eventId, eventType: btnType }
        }
        if (btnType === 'link') {
            $downloadButton.removeClass('hidden');
            openExternalLink($currentBtn);
        }

        jQuery.ajax(reqOptions).done(function (response) {
            $currentBtn.text(btnRegisteredText);
            $downloadButton.removeClass('hidden');
            if (btnType != 'link') {
                $currentBtnWrapper.addClass('disabled');
            }
        }).fail(function (error) {
            console.log(error.statusText);
        })
    }

    //-----Utils-----
    function getPagesCount(cardsCount, cardsPerPage) {
        if (cardsCount === 0 || cardsCount < cardsPerPage) {
            return 1;
        }

        return Math.ceil(cardsCount / cardsPerPage)
    }

    function getCardsPerPageCount() {
        var numberOfCardsOnPage = Number($allEventsContainer.attr('data-cardsOnPageCount'));

        if (numberOfCardsOnPage % 2 === 0) {
            return numberOfCardsOnPage;
        } else {
            return 8;
        }
    }

    function scrollToTop() {
        jQuery([document.documentElement, document.body]).animate({
            scrollTop: $allEventsContainer.offset().top
        }, 500);
    }
})

