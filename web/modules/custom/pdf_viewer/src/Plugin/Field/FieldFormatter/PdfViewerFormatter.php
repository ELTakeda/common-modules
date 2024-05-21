<?php

namespace Drupal\pdf_viewer\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'pdf_viewer_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "pdf_viewer_formatter",
 *   label = @Translation("PDF Viewer"),
 *   field_types = {
 *     "file",
 *     "link"
 *   }
 * )
 */
class PdfViewerFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
$text_link = $item->title;
if($text_link == '' || !isset($text_link)){
$text_link = 'Link to PDF';
}
      if ($item->isEmpty()) {
        continue;
      }
      $field_type = $item->getFieldDefinition()->getType();
      if ($field_type === 'file') {
        $file = $item->entity;
        $pdf_url = $file->createFileUrl(FALSE);

      } elseif ($field_type === 'link') {
        $url = $item->getUrl()->toString();
	$file = pdf_viewer_save_external_pdf($url,$item->getEntity());
        if ($file) {
          $pdf_url = $file->createFileUrl(FALSE);
        }
      } else {
        continue;
      }
      
      $elements[$delta] = [
        '#theme' => 'pdf_viewer_flipbook',
        '#pdf_url' => $pdf_url,
	'#text_link' => $text_link,
        '#attributes' => [
          'class' => ['pdf-viewer'],
          'data-pdf-url' => $pdf_url,
        ],
        '#attached' => [
          'library' => [
            'pdf_viewer/pdf_viewer',
            'pdf_viewer/pdf-js',
          ],
          'drupalSettings' => [
            'pdf_viewer' => [
              'pdfUrl' => $pdf_url,
              'pdfJsUrl' => '../../modules/pdf_viewer/libraries/pdf.js/build/pdf.js',
              'pdfJsWorkerUrl' => '../../modules/pdf_viewer/libraries/pdf.js/build/pdf.worker.js',
            ],
          ],
        ],
      ];

    }
    return $elements;
  }
//  public function accessProtected($obj, $prop) {
//     $reflection = new ReflectionClass($obj);
//     $property = $reflection->getProperty($prop);
//     $property->setAccessible(true);
//     return $property->getValue($obj);
//   }

}
