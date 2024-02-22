<?php

namespace Drupal\takeda_id\Controller;

use Drupal\Component\Utility\Random;
use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\user\UserStorageInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use \Drupal\Component\Utility\Crypt;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Component\Datetime\TimeInterface;
use \Drupal\user\Entity\User;
use Drupal\takeda_id\TakedaIdInterface;
use \GuzzleHttp\Exception\RequestException;
use Drupal\Component\Serialization\FormUrlEncodedEncoder;
use Drupal\Core\Datetime\DrupalDateTime;

use Drupal\jwt\Transcoder\JwtTranscoder;
use Drupal\Core\Site\Settings;

/**
 * Provides route responses for the Example module.
 */
class TakedaIdController extends ControllerBase
{

    /**
     * Define constants used by this class
     */
    const DRUPAL_MARKUP = '#markup';
    const DRUPAL_FRONTPAGE = '<front>';

    /**
     * The date formatter service.
     *
     * @var \Drupal\Core\Datetime\DateFormatterInterface
     */
    protected $dateFormatter;

    /**
     * The user storage.
     *
     * @var \Drupal\user\UserStorageInterface
     */
    protected $userStorage;

    /**
     * The Messenger service.
     *
     * @var \Drupal\Core\Messenger\MessengerInterface
     */
    protected $messenger;

    /**
     * The time service.
     *
     * @var \Drupal\Component\Datetime\TimeInterface
     */
    protected $time;

    /**
     * Constructs a TakedaIdController object.
     *
     * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
     *   The date formatter service.
     * @param \Drupal\user\UserStorageInterface $user_storage
     *   The user storage.
     * @param \Drupal\Core\Messenger\MessengerInterface $messenger
     *   The status message.
     * @param \Drupal\Component\Datetime\TimeInterface $time
     *   The time service.
     */
    public function __construct(DateFormatterInterface $date_formatter, UserStorageInterface $user_storage, MessengerInterface $messenger, TimeInterface $time)
    {
        $this->dateFormatter = $date_formatter;
        $this->userStorage = $user_storage;
        $this->messenger = $messenger;
        $this->time = $time;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('date.formatter'),
            $container->get('entity_type.manager')->getStorage('user'),
            $container->get('messenger'),
            $container->get('datetime.time')
        );
    }

    /**
     * Returns a simple page.
     *
     * @return array
     *   A simple renderable array.
     */
    public function leadCallback(Request $request)
    {
        \Drupal::service('page_cache_kill_switch')->trigger();

        $user = User::load(\Drupal::currentUser()->id());
        $userData = \Drupal::service('user.data');
        $parameters = json_decode($request->getContent(), true);
        $formRequest = $request->request->all();
        $requestParams = \Drupal::request()->query->all();
        $session = \Drupal::request()->getSession();


        \Drupal::logger('takeda_id')->notice('Got JSON Callback<pre><code>' . print_r($parameters, true) . '</code></pre>Got Form Callback<pre><code>' . print_r($formRequest, true) .'</code></pre>Got Query Params Callback<pre><code>' . print_r($requestParams, true) . '</code></pre>');

        if ($parameters && $parameters['Status']) {
            $account = user_load_by_mail($parameters['Email']);
            $config = \Drupal::config(TakedaIdInterface::CONFIG_OBJECT_NAME);

            switch ($parameters['Status']) {
                case 'Closed - Matched':
                    // User has been matched, access granted

                    // Get Customer ID from response
                    $customerId = null;
                    if ($parameters['Takeda_Enterprise_Account_ID_TPI__c']) {
                        $customerId = $parameters['Takeda_Enterprise_Account_ID_TPI__c'];
                    }

                    // Update profile
                    takeda_account_set_hcp_matched($account, $customerId);

                    // Send success notification
                    $approvedMail = \Drupal::service('plugin.manager.mail')->mail($module = 'takeda_id', $key = 'accepted', $account->getEmail(), $account->getPreferredLangcode(), ['account' => $account], $reply = null, $send = true);

                    break;

                case 'Closed - Rejected':
                    // User has been rejected, access denied

                    // Update profile
                    takeda_account_set_hcp_rejected($account);

                    // Send rejection notification
                    $rejectedMail = \Drupal::service('plugin.manager.mail')->mail($module = 'takeda_id', $key = 'rejected', $account->getEmail(), $account->getPreferredLangcode(), ['account' => $account], $reply = null, $send = true);

                    break;

                case 'Pending - Qualification':
                    // User is still pending qualification

                    // Update profile
                    takeda_account_set_hcp_pending($account, false);

                    break;

                default:
                    # Unknown status, no need to act
                    break;
            }

            return [
                self::DRUPAL_MARKUP => 'Request Processed.',
            ];
        }

        if ($user->id()) {
            $config = \Drupal::config(TakedaIdInterface::CONFIG_OBJECT_NAME);
            $is_first_login = $session->get('takeda_id_first_login');
            $first = $config->get('first_login_redirect') ?: ['route_name' => self::DRUPAL_FRONTPAGE, 'route_parameters' => []];
            $subsequent = $config->get('subsequent_login_redirect') ?: ['route_name' => self::DRUPAL_FRONTPAGE, 'route_parameters' => []];
            $lang_from_session =  $session->get('takeda_id_callback_lang');

            if ($lang_from_session) {
                $current_language = \Drupal::languageManager()->getLanguage($lang_from_session);
            } else {
                $current_language = \Drupal::languageManager()->getCurrentLanguage();
            }

            if ($config->get('multichannel_can_edit')) {
                // Build the URL to request.
                $url_api = $config->get('multichannel_api_url') . "/multichannelActivity";

                $headers = [
                    "Content-Type" => "application/json",
                    "client_id" => $config->get('api_key'),
                    "client_secret" => $config->get('api_secret'),
                ];

                $url = \Drupal::request()->getSchemeAndHttpHost();

                // Get the current date and time.
                $currentDateTime = new DrupalDateTime();
                // Get the currently set timezone.
                $timezone = date_default_timezone_get();
                // Set the time zone to the current.
                $currentDateTime->setTimezone(new \DateTimeZone($timezone));

                // Format the datetime string in the desired format.
                $datetimeString = $currentDateTime->format('Y-m-d\TH:i:s.v\Z');

                // Get accountId
                $crmId = $user->get('field_customer_id')->value;
                $position = strrpos($crmId, "-");

                if ($position !== false) {
                    $accountId = $desiredPart = substr($crmId, $position + 1);
                } else {
                    $accountId = $desiredPart = $crmId;
                }

                $requestBody = [
                    [
                        "accountId" => $accountId,
                        "countryCode" => $user->get('field_crm_country')->value,
                        "startDateTime" => $datetimeString,
                        "recordTypeName" => "Page_Visit",
                        "additionalDetails" => "Logged in " . $url,
                        "product" => "",
                        "url" => $url
                    ],
                ];

                $options = [
                    'headers' => $headers,
                    'json' => $requestBody
                ];

                try {
                    $response = \Drupal::httpClient()->post($url_api, $options);
                } catch (RequestException $e) {
                    watchdog_exception('takeda_id', $e);
                }
            }

            if ($user->hasField('field_profession')) {
                // Build the URL to request.
                $url = Url::fromUri($config->get('okta_url') . "/token");

                $auth = $config->get('okta_client_id') . ':' . $config->get('okta_client_secret');
                $authorization = 'Basic '. base64_encode ($auth);

                $language_none = \Drupal::languageManager()->getLanguage(LanguageInterface::LANGCODE_NOT_APPLICABLE);
                $callback_url = Url::fromRoute('takeda_id.lead_callback')
                    ->setAbsolute(true)
                    ->setOption('language', $language_none)
                    ->toString(true)
                    ->getGeneratedUrl();

                // Add query parameters to the URL.
                $query_params = [
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $callback_url,
                    'code' => $request->request->all()['code'],
                    'state' => $request->request->all()['state']
                ];

                $url->setOption('query', $query_params);

                // Set headers for the request.
                $headers = [
                    "Accept" => "application/json",
                    "Content-Type" => "application/x-www-form-urlencoded",
                    "Authorization" => $authorization
                ];

                $options = [
                    'headers' => $headers,
                ];

                try {
                    $response = \Drupal::httpClient()->post($url->toString(), $options);
                    $resp = $response->getBody();
                    $data = json_decode($resp);

                    $tokenPayload = $this->decryptJwtToken($data->id_token);

                    if(isset($tokenPayload['professsion'])) {
                        if ($user->get('field_profession')->value != $tokenPayload['profession'] || empty($user->get('field_profession')->value)) {
                            $user->set('field_profession', $tokenPayload['profession']);
                            $user->save();
                        }
                    }
                } catch (RequestException $e) {
                    watchdog_exception('takeda_id', $e);
                }
            }



            try {
                if ($is_first_login) {
                    $rediect_url = Url::fromRoute($first['route_name'], $first['route_parameters']);
                } else {
                    $rediect_url = Url::fromRoute($subsequent['route_name'], $subsequent['route_parameters']);
                }
                $rediect_url->setOption('language', $current_language);
                return new RedirectResponse($rediect_url->toString());
            } catch (\Throwable $e) {
                \Drupal::logger('takeda_id')->notice('User redirect on %FirstOrSubsequent login failed with error: %message', ['%FirstOrSubsequent' => $is_first_login ? 'first' : 'subsequent', '%message' => $e->getMessage()]);
                return $this->redirect(self::DRUPAL_FRONTPAGE, []);
            }
        }

        return [
            self::DRUPAL_MARKUP => 'Request Processed.',
        ];
    }

    private function decryptJwtToken(string $token)
    {
        list($header, $payload, $signature) = explode (".", $token);
        $payload_string = base64_decode($payload);
        $payload_array = json_decode($payload_string, true);
        return $payload_array;
    }

    /**
     * Returns a simple page.
     *
     * @return array
     *   A simple renderable array.
     */
    public function postLogin(Request $request)
    {
        \Drupal::service('page_cache_kill_switch')->trigger();

        // Ensure this page isn't cached
        $config = \Drupal::config(TakedaIdInterface::CONFIG_OBJECT_NAME);
        $user = User::load(\Drupal::currentUser()->id());

        /** @var UserDataInterface $userData */
        $userData = \Drupal::service('user.data');

        $authmap = \Drupal::service('externalauth.authmap');
        $authname = $authmap->get($user->id(), 'takeda_id');

        // Display base error messages based on user role if login is rejected
        if (\Drupal::currentUser()->id() !== 0 && $user->isBlocked()) {
            if ($user->hasRole('takeda_id_non_hcp')) {
                $message = $this->t('<h2>Sorry, the activation of your account could not be processed as we have not been able to identify you as a registered healthcare professional.</h2>');
            } elseif ($user->hasRole('takeda_id_unverified')) {
                $message = $this->t('<h2>Your account is currently pending verification.</h2><p>You will receive an email once your account has been verified.</p><p>You may be contacted by someone from Takeda to confirm your details.</p>');
            }

            return [
                self::DRUPAL_MARKUP => $message
            ];
        }

        // If logged in via takeda ID, redirect to Okta
        if ($authname && $config->get('okta_url')) {

            // This slightly awkward method of generating the callback url avoids triggering a "The controller result claims to be providing relevant cache metadata, but leaked metadata was detected" error when generating a URL for use in a TrustedRedirectResponse.
            // Ref: https://www.drupal.org/node/2630808

            $language_active =  \Drupal::languageManager()->getCurrentLanguage();
            $language_none = \Drupal::languageManager()->getLanguage(LanguageInterface::LANGCODE_NOT_APPLICABLE);
            $callback_url = Url::fromRoute('takeda_id.lead_callback')
                ->setAbsolute(true)
                ->setOption('language', $language_none)
                ->toString(true)
                ->getGeneratedUrl();

            $client_id = $config->get('okta_client_id');

            $session = \Drupal::request()->getSession();
            $sessionToken = $session->get('takeda_id_session_token');

            if ($language_active && $language_active->getId()) {
                $session->set('takeda_id_callback_lang', $language_active->getId());
            }

            // get nonce
            $random = new Random();
            $nonce = $random->string();

            $redirect_url = $config->get('okta_url') . '/authorize';
            $redirect_url .= '?client_id=' . $client_id;
            $redirect_url .= '&response_type=code&scope=openid%20profile%20email&response_mode=form_post&redirect_uri=' . urlencode($callback_url);
            $redirect_url .= '&state=nEwmKX64Rn1Q1glswrTR7yP5kmQDTcoW&nonce=' . urlencode($nonce) . '&sessionToken=' . $sessionToken;

            // Ensure the redirect isn't cached
            // Do redirect to okta
            return new TrustedRedirectResponse($redirect_url);
        } else {
            return $this->redirect('user.login');
        }
    }

    /**
     * Returns a simple page.
     *
     * @return array
     *   A simple renderable array.
     */
    public function verifyUser(Request $request, $user, $timestamp, $hash, $code)
    {
        $route_name = self::DRUPAL_FRONTPAGE;
        $route_options = [];
        $current_user = $this->currentUser();
        $account = User::load($current_user->id());

        // Verify that the user exists.
        if ($current_user === null || $user === null) {
            throw new AccessDeniedHttpException();
        }

        $user = \Drupal::service('entity.repository')->loadEntityByUuid('user', $user);
        $uid = $user->id();

        // When processing a one-time login link, we have to make sure that a user isn't already logged in.

        if ($current_user->isAuthenticated()) {
            // The existing user is already logged in.
            if ($account->uuid() == $user) {
                $this->messenger->addMessage($this->t('You are currently authenticated as user %user.', ['%user' => $current_user->getAccountName()]));
                // Redirect to user page.
                $route_name = 'entity.user.canonical';
                $route_options = ['user' => $current_user->id()];
            }
            // A different user is already logged in on the computer.
            else {
                $reset_link_account = $user;

                if (!empty($reset_link_account)) {
                    $this->messenger->addMessage($this->t(
                        'Another user (%other_user) is already logged in, but you tried to use a one-time link for user %resetting_user. Please <a href=":logout">log out</a> and try using the link again.',
                        [
                            '%other_user' => $current_user->getDisplayName(),
                            '%resetting_user' => $reset_link_account->getDisplayName(),
                            ':logout' => Url::fromRoute('user.logout')->toString(),
                        ]
                    ), 'warning');
                } else {
                    // Invalid one-time link specifies an unknown user.
                    $this->messenger->addMessage($this->t('You have tried to use a one-time login link that has either been used or is no longer valid. Please request a new one using the form below.'));
                    $route_name = 'user.pass';
                }
            }
        } else {

            // Time out, in seconds, until login URL expires. 24 hours = 86400
            // seconds / 7 days = 604800 seconds.
            $timeout = 604800;
            $currentTimestamp = $this->time->getRequestTime();
            $createdTimestamp = $timestamp - $timeout;

            // Search users to ensure the account is inactive and not already verified in Drupal
            $users = $this->userStorage->getQuery()
                ->accessCheck(FALSE)
                ->condition('uid', $uid)
                ->condition('status', 0)
                ->execute();

            // Timestamp can not be larger then current.
            /** @var \Drupal\user\UserInterface $account */
            if ($createdTimestamp <= $currentTimestamp && !empty($users) && $account = $this->userStorage->load(reset($users))
            ) {
                // Check if the verification code has expired and reject if so
                if (!$account->getLastLoginTime() && $currentTimestamp - $timestamp > $timeout) {
                    $this->messenger->addMessage($this->t('You have tried to use a one-time login link that has either been used or is no longer valid.'));
                    $route_name = self::DRUPAL_FRONTPAGE;
                } elseif ($account->id() && $timestamp >= $account->getCreatedTime() && !$account->getLastLoginTime() && $hash == user_pass_rehash($account, $timestamp)) {
                    $config = \Drupal::config(TakedaIdInterface::CONFIG_OBJECT_NAME);

                    // Set friendly date
                    $date = $this->dateFormatter->format($timestamp);

                    $this->getLogger('user')->notice('User %name used one-time login link at time %timestamp.', ['%name' => $account->getAccountName(), '%timestamp' => $date]);

                    // Validate with Takeda ID
                    $verification = takeda_id_api_verify($code);

                    if ($verification && $verification->status == "PASSWORD_RESET") {
                        // Activate the user in Drupal and update the access and login time.
                        $isVerified = takeda_account_set_verified($account, $currentTimestamp);

                        $customerId = $account->field_customer_id ? $account->field_customer_id->value : null;

                        /** @var UserDataInterface $userData */
                        $userData = \Drupal::service('user.data');
                        $takedaUserId = $account->field_takeda_id && $account->field_takeda_id->value ? $account->field_takeda_id->value : null;
                        $digitalId = $userData->get('takeda_id', $account->id(), 'digital_id');
                        $crmCountry = $account->field_crm_country->value ?: $config->get('default_country');

                        // Check user groups on Okta to determine if this user is already a HCP
                        $groups = takeda_id_check_user_groups($takedaUserId);
                        $userHasHcpGroup = false;
                        if ($groups && count($groups)) {
                            $userOktaGroups = array_column($groups, 'name');
                            $userHasHcpGroup = in_array('Okta-Takeda-Users', $userOktaGroups) || in_array('Takeda Users', $userOktaGroups) || in_array('HCP', $userOktaGroups);
                        }

                        // Check for presence of Customer ID and confirm if HCP Conversion is required
                        if ($customerId && $userHasHcpGroup) {
                            \Drupal::logger('takeda_id')->notice('HCP conversion bypassed for account ' . $account->id() . ' which has been directly registered as a HCP from supplied Customer ID ' . $customerId);

                            // Update account profile
                            takeda_account_set_hcp_matched($account, $customerId);

                            // Return success message
                            $this->messenger->addMessage($this->t('Your account has been activated and you may now login.'));
                        } else {
                            // Attempt HCP Conversion via the Takeda ID APIs
                            $hcpConversion = takeda_id_hcp_conversion($account, $customerId, $digitalId, $crmCountry);

                            if ($hcpConversion) {
                                // HCP Conversion response.
                                // Either:
                                // a) The User has been successfully registered matched as a HCP
                                // b) The user couldn't be automatically matched so a lead has been created

                                \Drupal::logger('takeda_id')->notice('Got HCP Conversion response for ' . $account->id() . '. Checking for desired status (ID / profile / customer_id / leadId): ' . var_export(isset($hcpConversion->id), true) . ' / ' . var_export(isset($hcpConversion->profile), true) . ' / ' . var_export(isset($hcpConversion->profile->customer_id), true) . ' / ' . var_export(isset($hcpConversion->leadId), true));

                                if (isset($hcpConversion->profile)) {

                                    // Get Customer ID from response
                                    $customerId = null;
                                    if(isset($hcpConversion->profile->customer_id) && $hcpConversion->profile->customer_id){
                                        $customerId = $hcpConversion->profile->customer_id;
                                    }

                                    // Update account profile
                                    takeda_account_set_hcp_matched($account, $customerId);

                                    // Return success message
                                    $this->messenger->addMessage($this->t('Your account has been activated and you may now login.'));
                                } elseif (isset($hcpConversion->leadId)) {
                                    takeda_account_set_hcp_pending($account, $hcpConversion->leadId);

                                    if ($config->get('require_hcp_match_to_activate')) {
                                        $this->messenger->addMessage($this->t('Your email address has been confirmed, however Takeda needs to verify your details to complete your enrollment. You will recieve an email once your account has been activated.'));
                                    } else {
                                        $this->messenger->addMessage($this->t('Your account has been activated. Takeda will verify your details to complete your enrollment.'));
                                    }
                                } else {
                                    \Drupal::logger('takeda_id')->notice('Unexpected result from HCP Conversion for ' . $account->id() . ' / <pre><code>' . print_r($hcpConversion, true) . '</code></pre>');

                                    $account->addRole('takeda_id_unverified');

                                    // Display warning
                                    $this->messenger->addMessage($this->t('Sorry, we were unable to confirm your Takeda ID registration. Please try again or contact Takeda for further assistance.'));
                                }
                            } else {
                                \Drupal::logger('takeda_id')->notice('Failed result from HCP Conversion for ' . $account->id() . ' / <pre><code>' . print_r($hcpConversion, true) . '</code></pre>');

                                // Display default welcome message.
                                $this->messenger->addMessage($this->t('Sorry, we were unable to confirm your Takeda ID registration. Please try again or contact Takeda for further assistance.'));
                            }
                        }

                        // Redirect to login.
                        $route_name = 'user.login';
                    } else {
                        // Deny access, Takeda validation failed
                        $this->messenger->addMessage($this->t('Unable to verify with Takeda ID. You may have tried to use a verification code that has already been used or is no longer valid.'));
                        $route_name = self::DRUPAL_FRONTPAGE;
                    }
                } else {
                    // Deny access, unable to use link
                    $this->messenger->addMessage($this->t('You have tried to use a verification code that has either been used or is no longer valid.'));

                    \Drupal::logger('takeda_id')->notice('Unable to process user verification for account ' . $account->id() . ' with verification: / <pre><code>' . print_r($verification, true) . '</code></pre>');

                    $route_name = self::DRUPAL_FRONTPAGE;
                }
            } else {
                // Deny access, unable to use link
                $this->messenger->addMessage($this->t('You have tried to use a verification code that has either been used or is no longer valid. Please try to login with your account.'));
                $route_name = self::DRUPAL_FRONTPAGE;
            }
        }
        return $this->redirect($route_name, $route_options);
    }
}
