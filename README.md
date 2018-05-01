# SQS Manager
[![Build Status](https://travis-ci.org/dirk39/sqs-manager.svg?branch=master)](https://travis-ci.org/dirk39/sqs-manager)

SQS Manager is a library that simplify fetch of messages from a SQS Queue. SQS Manager read messages from the queue you want, prepare a `SQSManager\Message` object and pass it to function or method you want. The main advantages of this libray are: 
 - After the message has been depleted will be deleted from the queue by the Manager. 
 - If an exception will be caught by the Manager, remaining messages will be released and caught exception will be thrown. 
 - If manager retrieve more than one message, Manager will pass messages one by one to designed method/function. After the execution the Manager will check visibilityTimeout and if near to expiration, it will extend according to the `VisibilityTimeout` value set and if timeout is less than 12 hours (max visibility timeout from SQS Documentation) for remaining messages.

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
 In `$additional_conf` array you can add custom configuration for the `Aws\Sqs\SqsClient` constructor [read docs](https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.Sqs.SqsClient.html#___construct).
 
 #### Fetch messages
 ```php
 
 ....
 
 $manager->run($queueName, $callback, $listenerConfigs);
 
 ```
 
