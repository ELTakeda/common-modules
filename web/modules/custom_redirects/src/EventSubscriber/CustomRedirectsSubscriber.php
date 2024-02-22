<?php

namespace Drupal\custom_redirects\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomRedirectsSubscriber implements EventSubscriberInterface
{
    public function checkForRedirection(RequestEvent $event)
    {
        
        // Default page redirects
        // Redirection functionality for the user pages
        $attr = $event->getRequest()->attributes;
        $current_route = $attr->get('_route');

        // Get the config form data
        $config = \Drupal::config('custom_redirects_config_form.settings');
        $custom_redirects_config_form_values = $config->get('custom_redirects_config_form_values') ?: []; // Blank array if no data so that it can be countable
        $custom_redirects_landing = !empty($custom_redirects_config_form_values['custom_redirects_landing']) ? $custom_redirects_config_form_values['custom_redirects_landing'] : 'none';
        $custom_redirect_login = !empty($custom_redirects_config_form_values['custom_redirects_login']) ? $custom_redirects_config_form_values['custom_redirects_login'] : '/user/login';
        $custom_redirect_register = !empty($custom_redirects_config_form_values['custom_redirects_register']) ? $custom_redirects_config_form_values['custom_redirects_register'] : '/user/register';
        $custom_redirect_profile = !empty($custom_redirects_config_form_values['custom_redirects_profile']) ? $custom_redirects_config_form_values['custom_redirects_profile'] : '/user';

        // User login
        if($current_route == 'user.login' && $custom_redirect_login !== '/user/login') {
            $this->redirectToRoute($event, $custom_redirect_login);
            return;
        }

        // User register
        if($current_route == 'user.register' && $custom_redirect_register !== '/user/register') {
            $method = $event->getRequest()->getMethod(); // GET or POST

            if ($method === 'GET') {
                $this->redirectToRoute($event, $custom_redirect_register);
                return;
            }
        }

        // User profile
        if($current_route == 'entity.user.canonical' && $custom_redirect_profile !== '/user') {
            $this->redirectToRoute($event, $custom_redirect_profile);
            return;
        }


        // If page is drupal's default register/login or takeda_id skip further functionality
        if ($current_route == 'user.login' 
            || $current_route == 'user.reset' 
            || $current_route == 'user.register' 
            || $current_route == 'takeda_id.post_login' 
            || $current_route == 'takeda_id.verify'
            || $current_route == 'takeda_id.lead_callback'
            || $current_route == 'entity.user.canonical'
            || $current_route == 'entity.media.canonical'
        ) {
            return;
        }

        $current_path = \Drupal::service('path.current')->getPath();

        if ($current_path == '/user/reset') return;
        if ($current_path == '/user/password') return;
        if ($current_path == '/email-existence-verification') return;

        // Get the database and current node
        $database = \Drupal::database();
        $node = \Drupal::routeMatch()->getParameter('node');

        $request = \Drupal::request();
        $session = $request->getSession();
        $countryTID = $session->get('userCountryTID');
        $redirecttodeeplink = $session->get('redirecttodeeplink');
        
        $tempstore = \Drupal::service('tempstore.private')->get('common_modules');
        $is_logged = \Drupal::currentUser()->isAuthenticated();

        if ($redirecttodeeplink && $is_logged) {
            $this->redirectToRoute($event, $redirecttodeeplink);
            $session->remove('redirecttodeeplink');
            return;
        } 

        // If it's a node
        // Get the node ID and the public access toggle value
        if ($node) {
            $nid = $node->id();

            $node_type = $node ? \Drupal::request()->attributes->get('node')->getType() : '';

            if ( $node_type == 'home_page' && !empty($_POST['selectCountry']) ){
                return;
            }

            if ( $node_type != 'landing_page' && empty($countryTID) && $custom_redirects_landing != 'none'){
                $this->redirectToRoute($event, $custom_redirects_landing);
                return;
            }

            if ( $node_type == 'login_page' && !empty($_GET['page']) ){
                $page_url = $_GET['page'];
                $session->set('redirecttodeeplink', $page_url);
            }

            if ( $node_type == 'login_page' && $current_route == 'entity.node.canonical' && $is_logged ){
                $this->redirectToRoute($event, $custom_redirect_profile);
                return;
            }
            
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        $events[KernelEvents::REQUEST][] = array('checkForRedirection', 31);
        return $events;
    }

    public function redirectToRoute($event_received, $route_to_redirect) {
        $base_url = $event_received->getRequest()->getBaseUrl();

        if (!$base_url) {
            $base_url = '/';
        }

        if ($base_url !== '/') {
            $route_to_redirect_replaced = str_replace($base_url, '', $route_to_redirect);
            $redirect = new RedirectResponse($base_url . $route_to_redirect_replaced, 301);
            $event_received->setResponse($redirect);
            return;
        }

        $redirect = new RedirectResponse($route_to_redirect, 301);
        $event_received->setResponse($redirect);
    }
}
