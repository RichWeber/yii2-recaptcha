# Yii2 extension for Google reCAPTCHA

ReCAPTCHA lets you embed a CAPTCHA in your web pages in order to protect them against spam and other types of automated abuse.

## Installation

Adding reCAPTCHA to your site consists of three steps:

1. [Getting started](http://www.google.com/recaptcha/admin)
2. Displaying the widget
3. Verifying the user's response

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require richweber/yii2-recaptcha "dev-master"
```

or add

```
"richweber/yii2-recaptcha": "dev-master"
```

to the ```require``` section of your `composer.json` file.

## Usage

### Component Configuration

```php
'components' => [
    ...
    'recaptcha' => [
        'class' => 'richweber\recaptcha\ReCaptcha',
        'siteKey' => 'https://www.google.com/recaptcha/admin',
        'secretKey' => 'https://www.google.com/recaptcha/admin',
        'errorMessage' => 'Are you robot?',
    ],
    ...
],
```

### Displaying the widget

```php
use richweber\recaptcha\Captcha;

<?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'subject') ?>
    <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>

    <?= Captcha::widget() ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    </div>
<?php ActiveForm::end(); ?>
```

### Verifying the user's response

```php
public function actionContact()
{
    $model = new ContactForm();
    if (
        $model->load(Yii::$app->request->post())
        && Yii::$app->recaptcha->verifyResponse(
            $_SERVER['REMOTE_ADDR'],
            Yii::$app->request->post('g-recaptcha-response'))
        && $model->contact(Yii::$app->params['adminEmail'])
    ) {
        Yii::$app->session->setFlash('contactFormSubmitted');

        return $this->refresh();
    } else {
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
}
```

### License

**yii2-recaptcha** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.