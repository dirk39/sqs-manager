<?php

namespace SQSManager;

class Message implements MessageInterface
{
  private $messageId;
  private $receiptHandle;
  private $body;
  private $attributes = [];
  private $messageAttributes = [];


  public function __construct($messageId, $receiptHandle, $md5OfBody, $body, $attributes = [], $messageAttributes = [])
  {
    $this->validateMessage($messageId, $receiptHandle, $md5OfBody, $body, $attributes, $messageAttributes);

    $this->messageId = $messageId;
    $this->receiptHandle = $receiptHandle;
    $this->body = $body;
    $this->attributes = $attributes;
    $this->messageAttributes = $messageAttributes;
  }

  private function validateMessage($messageId, $receiptHandle, $md5OfBody, $body, $attributes, $messageAttributes)
  {
    if(!is_string($messageId)) {
      throw new \InvalidArgumentException("messageId must be a string");
    }

    if(!is_string($receiptHandle)) {
      throw new \InvalidArgumentException("receiptHandle must be a string");
    }

    if(!is_string($md5OfBody)) {
      throw new \InvalidArgumentException("md5OfBody must be a string");
    }

    if(!is_string($body) || md5($body) !== $md5OfBody) {
      throw new \InvalidArgumentException("Invalid body");
    }

    if(!is_null($attributes) && !is_array($attributes))
    {
      throw new \InvalidArgumentException("attributes must be an array");
    }

    if(!is_null($messageAttributes) && !is_array($messageAttributes))
    {
      throw new \InvalidArgumentException("attributes must be an array");
    }

    return true;
  }

  public function getBody()
  {
    return $this->body;
  }

  public function getReceipt()
  {
    return $this->receiptHandle;
  }

  public function getAttributes()
  {
    return $this->getAttributes();
  }

  public function getMessageAttributes()
  {
    return $this->messageAttributes;
  }

}