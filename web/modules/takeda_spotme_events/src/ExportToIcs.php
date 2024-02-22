<?php

namespace Drupal\spotme_events;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Url;
/**
 * OrderHistory
 */
class ExportToIcs extends ControllerBase
{
    const TEMPLATE_KEY_EVENT_TITLE = '$EVENT_TITLE';
    const TEMPLATE_KEY_EVENT_STARTDATE = '$EVENT_STARTDATE';
    const TEMPLATE_KEY_EVENT_ENDDATE = '$EVENT_ENDDATE';
    const TEMPLATE_KEY_EVENT_LOCATION = '$LOCATION';
    const TEMPLATE_KEY_DESCRIPTION = '$DESCRIPTION';
    const TEMPLATE_KEY_CURRENT_LANGUAGE = '$LANGUAGE';
    const TEMPLATE_KEY_CURRET_TIME = '$CURRENT_TIME';

    const LOCATION_ONLINE_TEAMS = "Microsoft teams";
    
    /**
     * @return Response instance
     */
    public function export() {
        
        $query = \Drupal::request()->query->all();
        if (!empty($query)) {
            $currentTime = time();

            //get current language code
            $languagecode = \Drupal::languageManager()->getCurrentLanguage()->getId();

            $eventId = isset($query['event_id'])? $query['event_id'] : '';
            if ($eventId) {
                $event = Node::load($eventId)->getTranslation($languagecode);

                $user_id = \Drupal::currentUser()->id();
                $database = \Drupal::database();

                $field_event_id = $event->get('field_se_event_id')->value;

                //retrieve user data
                $result = $database->select('event_registration', 'ev')
                    ->fields('ev')
                    ->condition('user_id', $user_id)
                    ->condition('event_id', $field_event_id)
                    ->execute()
                    ->fetchAssoc();

                if (count($result) > 0) {
                    $takeda_enterprise_id = $result['takeda_id'];
                    $customer_country_code = $result['country_code'];
                }
                else {
                    $takeda_enterprise_id = '';
                    $customer_country_code = '';
                }
                //event title
                $event_title = $event->getTitle();

                //retrieve location and event place
                $location = $event->get('field_se_type')->value;
                $event_place = $event->get('field_se_place')->value;

                $spotme_url_query = \Drupal::service('spotme_events.curl_functions')->getSpotmeLiveURL($field_event_id, $takeda_enterprise_id, $customer_country_code);

                //retrieve activity link from the query
                $spotme_url_link = '';
                if(!empty($spotme_url_query)) {
                    $spotme_url_link = $spotme_url_query->activityLink;
                }
                //button functionality types
                $func_type = $event->get('field_se_button_functionality')->value; 
                switch ($func_type) {
                    case 'spotme':
                        if ($location === 'offline') {
                            $location = $event_place;
                        }
                        else {
                            $location = $spotme_url_link;
                        }
                        break;

                    case 'teams':
                        if ($location === 'offline') {
                            $location = $event_place;
                        }
                        else {
                            $location = self::LOCATION_ONLINE_TEAMS;
                        }
                        break;  

                    case 'link':
                        if ($location === 'online') {
                            $location = $event->get('field_se_link_external')->value;
                        }
                        else {
                            $location = $event_place;
                        }
                        break;
                }

                //retrieve event full url
                $pageUrl = $event->toUrl('canonical', ['absolute' => TRUE])->toString();
                
                //get description
                $description = $event->get('field_se_description')->value;
                $description = trim(strip_tags($description)).' - '.$pageUrl;

                //retrieve event start date
                $event_startdate = $event->get('field_se_start_date')->date->getTimestamp();

                //retrieve event end date
                $event_endtime = $event->get('field_se_end_date')->date->getTimestamp();

                //replace with "T" space between the 2 parts of the date due to the requirements of the ics format
                $event_startdate = date('Ymd His', $event_startdate);
                $event_startdate = str_replace(' ', 'T', $event_startdate);

                //replace with "T" space between the 2 parts of the date due to the requirements of the ics format
                $event_endtime = date('Ymd His', $event_endtime);
                $event_endtime = str_replace(' ', 'T', $event_endtime);

                $file = file_get_contents(getcwd() . '/'. drupal_get_path('module','spotme_events') . '/assets/ics/event-file.ics');

                // Replace the placeholder fields
                $file = str_replace(self::TEMPLATE_KEY_EVENT_TITLE, $event_title, $file);
                $file = str_replace(self::TEMPLATE_KEY_EVENT_STARTDATE, $event_startdate, $file);
                $file = str_replace(self::TEMPLATE_KEY_EVENT_ENDDATE, $event_endtime, $file);
                $file = str_replace(self::TEMPLATE_KEY_EVENT_LOCATION, $location, $file);
                $file = str_replace(self::TEMPLATE_KEY_CURRET_TIME, $currentTime, $file);
                $file = str_replace(self::TEMPLATE_KEY_DESCRIPTION, $description, $file);
                $file = str_replace(self::TEMPLATE_KEY_CURRENT_LANGUAGE, $languagecode, $file);
                    
                // return it as a response.
                $response = new Response();

                // By setting these 2 header options, the browser will see the URL.
                $response->headers->set('Content-Type', 'text/calendar');
                $response->headers->set('Content-Disposition', 'attachment; filename="single-event.ics"');
                $response->setContent($file);
                return $response;
          }
        }
    }
}