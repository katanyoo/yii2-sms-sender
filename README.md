SMS Sender
==========
This extension for send SMS to all number in Thailand

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist katanyoo/yii2-sms-sender "*"
```

or add

```
"katanyoo/yii2-sms-sender": "*"
```

to the require section of your `composer.json` file.


Usage
-----

To use this extension, simply add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        'smsSender' => [
            'class' => 'katanyoo\smssender\smsSender',
            'provider_endpoint' => '<ENDPOINT>',
            'username' => '<USERNAME>',
            'password'=> '<PASSWORD>',
        ]
    ],
];
```

You can then send an sms as follows:

```php
Yii::$app->smsSender
     ->setMobileNo('08xxxxxxxx')
     ->setMessage('your message')
     ->send();
```