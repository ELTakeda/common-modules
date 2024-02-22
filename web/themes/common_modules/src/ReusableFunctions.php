<?php
declare(strict_types = 1);

namespace Drupal\common_modules;

use Drupal\taxonomy\Entity\Term;


class ReusableFunctions
{

    private function __construct()
    {
        
    }

    /**
     * @param string $taxonomy_name
     * 
     * @return array
     */
    public static function getTaxonomyTermNames($machine_name): array {
        $tids = \Drupal::entityQuery('taxonomy_term')
            ->accessCheck(TRUE)
            ->condition('vid', $machine_name)
            ->condition('status', 1)
            ->sort('weight','ASC')
            ->execute();
        $terms = Term::loadMultiple($tids);
        $taxonomy_terms = [];

        if ($terms) {
            $data = [];
            foreach ($terms as $term) {
                $term = trim($term->getName());
                $data['term_name'] = $term;
                $data['term_id'] = preg_replace('/[^a-zA-Z0-9 ]/m', '-', strtolower($term));
                $taxonomy_terms[] = $data;
            }
        }

        return $taxonomy_terms;
    }
}
