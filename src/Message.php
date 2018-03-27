<?php

class Message implements MessageInterface
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
    $this->queueUrl = $queueUrl;
    $this->body = $message['Body'];
    $this->receiptHandle = $message['ReceiptHandle'];
    $this->messageId = $message['MessageId'];
    $this->client = $client;
  }

  public function getBody()
  {
    return $this->body;
  }

  public function getReceipt()
  {
    return $this->receiptHandle;
  }

  public function deleteMessage()
  {
    if($this->deleted)
    {
      return true;
    }

    $this->client->deleteMessage([
      'QueueUrl' => $this->queueUrl,
      'ReceiptHandle' => $this->receiptHandle
    ]);

    $this->deleted = true;

    return true;
  }

}