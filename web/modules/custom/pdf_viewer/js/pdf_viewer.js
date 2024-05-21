(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.pdfViewer = {
    attach: function (context, settings) {
      if (typeof drupalSettings.pdf_viewer === 'undefined') {
        return;
      }
      console.log('PDF Viewer behavior attached');

      $('.pdf-viewer', context).once('pdf-viewer').each(function () {
        const $container = $(this);
        const pdfUrl = $container.data('pdf-url');
        console.log('Processing PDF Viewer with URL:', pdfUrl);

        const pdfRender = $container.find('.pdf-render')[0];
        const prevBtn = $container.find('.prev-btn')[0];
        const nextBtn = $container.find('.next-btn')[0];
        const openPdfBtn = $container.find('.open-pdf-btn')[0];

        let pdfDoc = null;
        let pageNum = 1;

        if (!pdfRender) return;

        $.getScript(drupalSettings.pdf_viewer.pdfJsUrl, function () {
          pdfjsLib.GlobalWorkerOptions.workerSrc = drupalSettings.pdf_viewer.pdfJsWorkerUrl;

          // Add the withCredentials option here
          pdfjsLib.getDocument({ url: pdfUrl, withCredentials: true }).promise.then((pdf) => {
            pdfDoc = pdf;
            renderPage(pageNum);
          });

          function renderPage(num) {
            pdfDoc.getPage(num).then((page) => {
              const viewport = page.getViewport({ scale: 2 });
              pdfRender.width = viewport.width;
              pdfRender.height = viewport.height;

              const renderContext = {
                canvasContext: pdfRender.getContext('2d'),
                viewport: viewport,
              };

              page.render(renderContext);
            });
          }

          prevBtn.addEventListener('click', () => {
            if (pageNum <= 1) return;
            pageNum--;
            renderPage(pageNum);
          });

          nextBtn.addEventListener('click', () => {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            renderPage(pageNum);
          });

          openPdfBtn.addEventListener('click', () => {
            window.open(pdfUrl, '_blank');
          });
        });
      });
    },
  };
})(jQuery, Drupal, drupalSettings);
