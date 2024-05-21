(function ($, Drupal, drupalSettings) {
    let scale = 1.5;
    let loader = jQuery('.js-loader');
    let icon_select_frontend_loaded = false;

    Drupal.behaviors.pdfViewerFlipbook = {
        attach: function (context, settings) {
            // Load only once.
            if (icon_select_frontend_loaded) {
                return;
            }
            icon_select_frontend_loaded = true;
            const pdfUrl = drupalSettings.pdf_viewer.pdfUrl;
            const flipbook = jQuery('#flipbook', context);

            let originalPageWidth = 0;
            let originalPageHeight = 0;

            function renderFlipbook() {
                flipbook.empty();

                getPDFPage();
            }

            function getPDFPage() {
                loader.removeClass('loader-hidden');
                pdfjsLib.getDocument(pdfUrl).promise.then(function (pdf) {
                    pdfData = pdf;
                    const numPages = pdf.numPages;

                    for (let pageNum = 1; pageNum <= numPages; ++pageNum) {
                        const pageContainer = jQuery('<div/>');
                        flipbook.append(pageContainer);

                        numPage = pageNum;

                        pdf.getPage(pageNum).then(function (page) {
                            const viewport = page.getViewport({ scale: scale });
                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            pageContainer.append(canvas);

                            const renderContext = {
                                canvasContext: context,
                                viewport: viewport,
                            };

                            page.render(renderContext).promise.then(function () {
                                if (pageNum === 1) {
                                    flipbook.turn({
                                        width: viewport.width * 2,
                                        height: viewport.height,
                                        elevation: 50,
                                        gradients: true,
                                        autoCenter: false,
                                        inclination: 50,
                                    });

                                    const $firstPage = $('.p1');

                                    originalPageWidth = $firstPage.width();
                                    originalPageHeight = $firstPage.height();

                                    attachEventListeners();
                                    flipbook.bind('start', function (event, pageObject, corner) {
                                        if (corner == 'tl' || corner == 'tr' || corner == 'bl' || corner == 'br') {
                                            event.preventDefault();
                                        }
                                    });
                                }
                            });
                        });
                    }
                });
            }

            const $fullscreenBtn = $('.js-fullscreen');
            const $viewerContainer = $('.pdf-viewer-container');

            const originalHorizontalPadding = $viewerContainer.innerWidth() - $viewerContainer.width();
            const originalVerticalPadding = $viewerContainer.innerHeight() - $viewerContainer.height();

            let isEnteringFullscreen = true;

            function attachEventListeners() {
                loader.addClass('loader-hidden');

                $(context).find('.left-arrow').on('click', goPrevious);
                $(context).find('.right-arrow').on('click', goNext);

                $fullscreenBtn.on('click', openFullscreen);

                $(document).on('fullscreenchange', fullscreenToggler);

                $(window).on('resize', function () {
                    if (isEnteringFullscreen == false) {
                        isEnteringFullscreen = true;
                        fullscreenToggler();
                    }
                });
            }

            function openFullscreen(e) {
                if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                } else {
                    element = $('.pdf-viewer-container').get(0);
                    if (element.requestFullscreen) {
                        element.requestFullscreen();
                    } else if (element.mozRequestFullScreen) {
                        element.mozRequestFullScreen();
                    } else if (element.webkitRequestFullscreen) {
                        element.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                    } else if (element.msRequestFullscreen) {
                        element.msRequestFullscreen();
                    }
                }
            }

            function fullscreenToggler(e) {
                let newContainerPadding;

                if (isEnteringFullscreen) {
                    newContainerPadding = calculateAndApplyFullscreenDimentions();
                } else {
                    newContainerPadding = returnToOriginalSize();
                }

                $viewerContainer.css('padding', newContainerPadding);
                flipbook.turn('resize');
            }

            function calculateAndApplyFullscreenDimentions() {
                const containerWidth = $viewerContainer.innerWidth() - originalHorizontalPadding;
                const containerHeight = $viewerContainer.innerHeight() - originalVerticalPadding;
                const ratio = originalPageHeight / originalPageWidth;
                const pageToContainerHeightDiff = containerHeight - (containerWidth / 2) * ratio;
                let newContainerPadding;
                isEnteringFullscreen = false;

                if (pageToContainerHeightDiff < 0) {
                    newPageHeight = containerHeight;
                    newPageWidth = newPageHeight / ratio;

                    const pixelsForPaddingLeft = Math.ceil(containerWidth - newPageWidth * 2 + originalHorizontalPadding);
                    newContainerPadding = '' + originalVerticalPadding / 2 + 'px ' + pixelsForPaddingLeft / 2 + 'px';
                } else {
                    newPageWidth = containerWidth / 2;
                    newPageHeight = (containerWidth / 2) * ratio;

                    const pixelsForPaddingLeft = Math.ceil(Math.abs(pageToContainerHeightDiff)) + originalVerticalPadding;
                    newContainerPadding = '' + pixelsForPaddingLeft / 2 + 'px ' + originalHorizontalPadding / 2 + 'px';
                }

                $('.page').each(function () {
                    $(this).width(newPageWidth);
                    $(this).height(newPageHeight);
                });

                return newContainerPadding;
            }

            function returnToOriginalSize() {
                const newContainerPadding = '' + originalVerticalPadding / 2 + 'px ' + originalHorizontalPadding / 2 + 'px';
                isEnteringFullscreen = true;

                $('.page').each(function () {
                    $(this).width(originalPageWidth);
                    $(this).height(originalPageHeight);
                });

                return newContainerPadding;
            }

            function goPrevious() {
                const currentPage = flipbook.turn('page');
                if (currentPage > 1) {
                    flipbook.turn('previous');
                }
            }

            function goNext() {
                const currentPage = flipbook.turn('page');
                const totalPages = flipbook.turn('pages');
                if (currentPage < totalPages) {
                    flipbook.turn('next');
                }
            }

            if (pdfUrl) {
                renderFlipbook(scale);
            }
        },
    };
})(jQuery, Drupal, drupalSettings);
