Yii2 Messente SMS
=================
Messente SMS Sending for Yii2

Inspired heavily by Yii2's emailing code

[![Build Status](https://travis-ci.org/mikk150/yii2-messentesms.svg?branch=master)](https://travis-ci.org/mikk150/yii2-messentesms) [![codecov](https://codecov.io/gh/mikk150/yii2-messentesms/branch/master/graph/badge.svg)](https://codecov.io/gh/mikk150/yii2-messentesms)

Usage
-----

To use this extension, simply add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        'sms' => [
            'class' => 'mikk150\sms\messente\Provider',
            'username' => 'messenteusername',
            'password' => 'messentepassword',
        ],
    ],
];
```

You can then send an SMS as follows:

```php
Yii::$app->sms->compose('Your awesome SMS')
     ->setFrom('Yii2')
     ->setTo('+15417543010')
     ->send();
```