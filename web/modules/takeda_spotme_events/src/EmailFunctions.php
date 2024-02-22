<?php

namespace Drupal\spotme_events;

use SendGrid\Email;
use SendGrid\Client;
use Exception;

class EmailFunctions {

    public function triggerTeamsEmail($stakeholder_email){
        
        $database = \Drupal::database();
        $condition_time_end = date('Y-m-d H:i:s',time());
        $condition_time_start = date('Y-m-d H:i:s', strtotime('-3 days'));
        $condition_type = 'teams';
        $condition_sent = 0; //'not sent' value for searching
        $to_update_value = 1; //'sent' value to update after sending

        $table_event_reg = 'event_registration';
        
        $user_data = $database->select($table_event_reg, 'ev')
            ->fields('ev')
            ->condition('event_type', $condition_type)
            ->condition('is_sent', $condition_sent)
            ->condition('created_on', $condition_time_start,'>=')
            ->condition('created_on', $condition_time_end,'<=')
            ->execute()
            ->fetchAll();
        
        if(count($user_data) > 0){
            $from_email = \Drupal::config('system.site')->get('mail');
            $from_name = \Drupal::request()->getHost();
            $subject = 'Registered users - Teams';
            $apiKey = \Drupal::config('sendgrid_integration.settings')->get('apikey');
            $html = \Drupal::service('spotme_events.email_functions')->html_build($user_data);

            if(empty($apiKey)){
                exit('No api key set!');
            }
            
            $email_send = \Drupal::service('spotme_events.email_functions')->sendEmail($from_email, $from_name, $subject, $stakeholder_email, $apiKey, $html);

            if($email_send == 'Success'){
                \Drupal::logger('spotme_events')->notice('Email sent!');

                $status = 'Email sent!';

                //Update database with new values
                $database->update($table_event_reg)
                ->fields(array('is_sent' => $to_update_value))
                ->condition('event_type', $condition_type)
                ->condition('is_sent', $condition_sent)
                ->condition('created_on', $condition_time_start,'>=')
                ->condition('created_on', $condition_time_end,'<=')
                ->execute();

                $database->insert('email_cron_log')
                ->fields([
                    'last_execution' => date('Y-m-d H:i:s',time()),
                ])
                ->execute();
            }else{
                \Drupal::logger('spotme_events')->notice('Email error!');

                $status = 'Email error!';
            }
        }else{
            \Drupal::logger('spotme_events')->notice('No new users to send.');

            $status = 'No new users to send.';
        }

        return $status;
    }
    
    public function html_build($data){
        $all_user_ids = array();
        $html = '';

        $html .= '
        <!DOCTYPE html>
        <html>
            <head>
                <style type="text/css">
                    #resultTable, #resultTable th, #resultTable td {
                        border: 1px solid black;
                        border-collapse: collapse;
                    }
                </style>
            </head>
            <body>
                <table id="resultTable" style="width:50%">
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th> 
                        <th>Email</th> 
                        <th>Event Title</th>
                        <th>TakdeID</th>
                        <th>Date of Register</th>
                    </tr>
        ';

        foreach($data as $table_data){
            $user_id = $table_data->user_id;

            array_push($all_user_ids, $user_id);
        }

        $database = \Drupal::database();

        $user_query = $database->select('users_field_data', 'ud');
        $user_query->join('user__field_first_name', 'fn', 'ud.uid = fn.entity_id');
        $user_query->join('user__field_last_name', 'ln', 'ud.uid = ln.entity_id');
        $user_query->join('user__roles', 'ur', 'ud.uid = ur.entity_id');

        $user_query->fields('ud', ['uid', 'mail'])
        ->fields('fn',  ['field_first_name_value'])
        ->fields('ln',  ['field_last_name_value'])
        ->fields('ur',  ['roles_target_id'])
        ->condition('ud.uid', $all_user_ids, 'IN');

        $results = $user_query
        ->execute()
        ->fetchAll();

        foreach($data as $table_data){

            $user_id = $table_data->user_id;
            $event_id = $table_data->event_id;

            $event_info = \Drupal::entityTypeManager()
            ->getStorage('node')
            ->loadByProperties([
                'field_se_event_id' => $event_id
            ]);

            $event_title = array_shift($event_info)->getTitle();

            foreach($results as $user_info){
                $info_uid = $user_info->uid;
                
                if($user_id == $info_uid){
                    $first_name = $user_info->field_first_name_value;
                    $last_name = $user_info->field_last_name_value;

                    break;
                }
            }

            $html .= '
                    <tr>
                        <td>'.$first_name.'</td>
                        <td>'.$last_name.'</td>
                        <td>'.$table_data->email.'</td>
                        <td>'.$event_title.'</td>
                        <td>'.$table_data->takeda_id.'</td>
                        <td>'.$table_data->created_on.'</td>
                    </tr>';
        }

        $html .= '
                </table>
            </body>
        </html>';

        return $html;    
    }

    public function sendEmail($from_email, $from_name, $subject, $receiver_email, $apiKey, $html){
       
        //prepare and send email
        $email = new Email();
        $email->setFrom($from_email);
        $email->setFromName($from_name);
        $email->setSubject($subject);
        $email->addTo($receiver_email);
        
        if(!empty($html)){
            $email->setHtml($html);
        }else{
            $email->setText('No html');
        }

        $sg = new Client($apiKey);

        try {
            $response = $sg->send($email);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }

        if($response){
            return 'Success';
        }else{
            return 'Error';
        }
    }
}