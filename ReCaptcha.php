<?php
/**
 * @copyright Copyright &copy; Roman Bahatyi, richweber.net, 2014
 * @package yii2-recaptcha
 * @version 1.0.0
 */

namespace richweber\recaptcha;

/**
 * Yii2 extension for Google reCAPTCHA
 *
 * To use reCAPTCHA, you need to sign up for an API key pair for your site.
 * The key pair consists of a site key and secret. The site key is used to display
 * the widget on your site. The secret authorizes communication between
 * your application backend and the reCAPTCHA server to verify
 * the user's response. The secret needs to be kept safe for security purposes.
 *
 * Usage:
 * ~~~
 * 'components' => [
 *       ...
 *       'recaptcha' => [
 *           'class' => 'richweber\recaptcha\ReCaptcha',
 *           'siteKey' => 'https://www.google.com/recaptcha/admin',
 *           'secretKey' => 'https://www.google.com/recaptcha/admin',
 *           'errorMessage' => 'Are you robot?',
 *       ],
 *       ...
 *   ],
 * ~~~
 * @link https://www.google.com/recaptcha/admin#list
 * @author Roman Bahatyi <rbagatyi@gmail.com>
 * @since 1.0
 */
class ReCaptcha
{
    /**
     * The site key
     * @var string
     */
    public $siteKey = '';

    /**
     * The secret key for verify the user's response
     * @var string
     */
    public $secretKey = '';

    /**
     * Returns a value indicating whether there is any validation error
     * @var boolean
     */
    public $hasError = false;

    /**
     * Error message
     * @var string
     */
    public $errorMessage = '';

    /**
     * Url path to recaptcha server
     * @var string
     */
    private $siteVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify?';

    /**
     * Encodes the given data into a query string format.
     *
     * @param array $data array of string elements to be encoded.
     * @return string - encoded request.
     */
    private function encodeQS($data)
    {
        $request = '';
        foreach ($data as $key => $value) {
            $request .= $key . '=' . urlencode(stripslashes($value)) . '&';
        }
        $request = substr($request, 0, strlen($request)-1);
        return $request;
    }

    /**
     * Submits an HTTP GET to a reCAPTCHA server.
     *
     * @param string $path url path to recaptcha server.
     * @param array  $data array of parameters to be sent.
     * @return array response
     */
    private function submitHttpGet($path, $data)
    {
        $request = $this->encodeQS($data);
        $response = file_get_contents($path . $request);
        return $response;
    }

    /**
     * Calls the reCAPTCHA siteverify API to verify whether the user passes
     * CAPTCHA test.
     *
     * @param string $remoteIp   IP address of end user.
     * @param string $response   response string from recaptcha verification.
     * @return check result
     */
    public function verifyResponse($remoteIp, $response)
    {
        if ($response == null || strlen($response) == 0) {
            $this->hasError = true;
            return false;
        }

        $getResponse = $this->submitHttpGet(
            $this->siteVerifyUrl,
            [
                'secret' => $this->secretKey,
                'remoteip' => $remoteIp,
                'response' => $response
            ]
        );

        $answers = json_decode($getResponse, true);
        if ($answers['success'] == false) $this->hasError = true;
        return $answers['success'];
    }
}
