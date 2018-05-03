# SQS Manager
[![Build Status](https://travis-ci.org/dirk39/sqs-manager.svg?branch=master)](https://travis-ci.org/dirk39/sqs-manager)

SQS Manager is a library that simplifies fetching of messages from a SQS Queue. SQS Manager reads messages from the queue you want, prepares a `SQSManager\Message` object and pass it to function or method you define. The main advantages of this libray are: 
 - After the message has been depleted will be deleted from the queue by the Manager. 
 - If an exception is caught by the Manager, remaining messages will be released and the caught exception will be thrown. 
 - If the Manager retrieves several messages, it will pass them one by one to designed method/function. Afterwards the Manager will check `VisibilityTimeout` of remaining messages and in case of short expiry date, it will extend the expiration according to the `VisibilityTimeout` value. According to the [AWS SQS documetation](https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-visibility-timeout.html), the maximum timeout is 12 hours.

## Installation
SQSManager can be installed by composer
```
composer install dirk39/sqs-manager
```

## Documentation
Usage of SQS Manager is very straightforward.

#### Construct
```php
require_once 'vendor/autoload.php';

$manager = new SQSManager\Manager('AWS_APP_ID', 'AWS_APP_SECRET', 'AWS_REGION', $additional_conf);

 ```
 In `$additional_conf` array you can add custom configuration for the `Aws\Sqs\SqsClient`. You can find more information about additional conf [here](https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.Sqs.SqsClient.html#___construct).
 
 #### Fetch messages
 ```php
 
 ....
 
 $manager->run($queueName, $callback, $listenerConfigs);
 
 ```
  - `$queueName`: the name of the queue you need to fetch. `$queue` could be the name of the queue or its url.
  - `$callback`: $callback value will be passed to a `call_user_func`. [Read the docs](http://php.net/manual/en/function.call-user-func.php) about acceptable $callback values. The Manager will pass a `SQSManager\Message` object to your callback function/method.
 - `$listenerConfigs`: additional configs pass to `Aws\Sqs\SqsClient::receiveMessage` method. For further information about allowed configs read [here](https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#receivemessage)

#### Manager configurators
 - `changeVisibilityTimeout`: change default `VisibilityTimeout` value. It must be an integer between and 0 and 43200 seconds (i.e. 12 hours)
 - `setWaitTimeSeconds`: change default `WaitTimeSeconds` value. It must be an integer between and 1 and 20 seconds.
 - `setMaxNumberOfMessages`: change default `MaxNumberOfMessages` value. It must be an integer between and 1 and 10.
 
