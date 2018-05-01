# SQS Manager
[![Build Status](https://travis-ci.org/dirk39/sqs-manager.svg?branch=master)](https://travis-ci.org/dirk39/sqs-manager)

SQS Manager is a library that simplify the access to messages stored into a SQS queue. SQS Manager read messages from the queue you want, prepare a `SQSManager\Message` object and pass it to function or method you want. After the message has been depleted will be deleted from the queue by the Manager. If an exception will be caught by the Manager, remaining messages will be released and caught exception will be thrown. 

## Documentation
