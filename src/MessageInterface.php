<?php

interface MessageInterface
{

  /**
   * MessageInterface constructor.
   * @param $messageId
   * @param $receiptHandle
   * @param $md5OfBody
   * @param $body
   * @param array $attributes
   * @param $md5OfMessageAttributes
   * @param array $messageAttributes
   */
  public function __construct($messageId, $receiptHandle, $md5OfBody, $body, array $attributes = [],
    $md5OfMessageAttributes, array $messageAttributes = []);

  /**
   * @return string
   */
  public function getReceipt();

  /**
   * @return string
   */
  public function getBody();

  /**
   * @return array
   */
  public function getAttributes();

  /**
   * @return array
   */
  public function getMessageAttributes();

}