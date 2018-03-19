<?php

use Aws\Sqs\SqsClient;

class SQSListener
{
  /** @var Aws\Sqs\SqsClient */
  private $client;
  private $lockHandler;

  public function __construct($appId, $appSecret, $region = 'eu-west-1')
  {
    $this->client = SqsClient::factory([
      'key' => $appId,
      'secret' => $appSecret,
      'region' => $region
    ]);

    $this->lockHandler = new \Symfony\Component\Filesystem\LockHandler($this->getTempFileName());
  }

  /**
   * @param $queueName
   * @param bool $alwaysListening
   */
  public function run($queueName, $alwaysListening = false)
  {
    if($alwaysListening && !$this->setPermanentListener())
    {
      return;
    }
  }

  protected function setPermanentListener()
  {
    if($this->lockHandler->lock())
    {
      return true;
    }

    return false;
  }

  /**
   * @return string
   */
  protected function getTempFileName()
  {
    return sha1(__CLASS__).'.lock';
  }

}