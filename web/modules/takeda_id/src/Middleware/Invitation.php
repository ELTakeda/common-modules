<?php

namespace Drupal\takeda_id\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Drupal\takeda_id\TakedaIdInterface;

class Invitation implements HttpKernelInterface {

    /**
     * The kernel.
     *
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected $httpKernel;

    /**
     * Constructs the FirstMiddleware object.
     *
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
     *   The decorated kernel.
     */
    public function __construct(HttpKernelInterface $http_kernel) {
        $this->httpKernel = $http_kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE): Response {
        $responseTest = new Response();
        $cookie = new Cookie('STYXKEY_test1','test1', 0, '/' , NULL, FALSE);
        $responseTest->headers->setCookie($cookie);
        /* $response->send();*/

        // Capture and process InvitationToken parameter
        $invitationToken = \Drupal::request()->query->get('InvitationToken');
        if($invitationToken){
          $tokenData = takeda_id_api_get_invitation($invitationToken);

          if ($tokenData && is_array($tokenData)) {
            $storedData = [];
            $storedData['QueryStrings'] = array_merge(\Drupal::request()->query->all(), $tokenData);

            // Set a cookie
            // We use a standard PHP session cookie rather than Drupal's session to ensure it is cleared on browser close
            setcookie(TakedaIdInterface::INVITATION_COOKIE_NAME, json_encode($storedData), [
                'expires' => 0, 
                'path' => '/', 
                'secure' => true, 
                'httponly' => false,
                'samesite' => 'Strict'
                ]
            );
            $cookieInvitation = new Cookie(
                TakedaIdInterface::INVITATION_COOKIE_NAME,
                json_encode($storedData),
                0,
                '/' ,
                NULL,
                FALSE
            );
              $response = new Response();
              $response->headers->setCookie($cookieInvitation);
            // Store to cookies object to support accessing in the current request
            $_COOKIE[TakedaIdInterface::INVITATION_COOKIE_NAME] = json_encode($storedData);
          }
        }

        // Capture and process FormType parameter
       

        return $this->httpKernel->handle($request, $type, $catch);
    }
    
}