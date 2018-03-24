<?php

class Message
{
  private $messageId;
  private $receiptHandle;
  private $body;
  private $deleted = false;
  private $client;
  private $queueUrl;

  /**
   * Message constructor.
   * @param array $message
   * @param $queueUrl
   * @param \Aws\Sqs\SqsClient $client
   */
  public function __construct(array $message, $queueUrl, \Aws\Sqs\SqsClient $client)
  {
    $this->validateMessage($message);

  }

}