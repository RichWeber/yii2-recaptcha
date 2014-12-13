<?php
/**
 * @copyright Copyright &copy; Roman Bahatyi, richweber.net, 2014
 * @package yii2-recaptcha
 * @version 1.0.0
 */

namespace richweber\recaptcha;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Yii2 extension for Google reCAPTCHA
 *
 * Usage:
 * ```
 * use richweber\recaptcha\Captcha;
 *
 * echo Captcha::widget();
 * ```
 * Optional. Forces the widget to render in a specific language.
 * Auto-detects the user's language if unspecified.
 *
 * Optional. The color theme of the widget.
 *
 * Usage:
 * ```
 * use richweber\recaptcha\Captcha;
 *
 * echo Captcha::widget([
 *     'lang' => 'en',
 *     'theme' => 'light' // or dark
 * ]);
 * ```
 *
 * @author Roman Bahatyi <rbagatyi@gmail.com>
 * @since 1.0
 */
class Captcha extends Widget
{
    /**
     * A specific language
     * @var string
     */
    public $lang;

    /**
     * The color theme of the widget.
     * @var string
     */
    public $theme;

    /**
     * Error message
     * @var string
     */
    public $errorMessage = 'Please, confirm you are not robot';

    /**
     * Initialize the widget
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (empty(Yii::$app->recaptcha->siteKey)) {
            throw new InvalidConfigException('The site key has not been set.');
        }
        if (empty(Yii::$app->recaptcha->secretKey)) {
            throw new InvalidConfigException('The secret key has not been set.');
        }
        if (!empty(Yii::$app->recaptcha->errorMessage)) {
            $this->errorMessage = Yii::$app->recaptcha->errorMessage;
        }

        if (empty($this->theme)) {
            $this->theme = 'light';
        } else {
            if ($this->theme != 'light' && $this->theme != 'dark') {
                throw new InvalidConfigException('The theme has not been set.');
            }
        }

        if ($this->lang) {
            echo Html::jsFile('https://www.google.com/recaptcha/api.js?hl=' . $this->lang);
        } else {
            echo Html::jsFile('https://www.google.com/recaptcha/api.js');
        }

        if (Yii::$app->recaptcha->hasError) {
            echo Html::tag('p', $this->errorMessage, ['class' => 'text-danger']);
        }

        echo Html::tag('div', '', [
            'class' => 'g-recaptcha',
            'data-sitekey' => Yii::$app->recaptcha->siteKey,
            'data-theme' => $this->theme,
        ]);
    }

}