<?php

use Aws\Sqs\SqsClient;
use \Symfony\Component\Filesystem\LockHandler;

class SQSListener
{
  /** @var Aws\Sqs\SqsClient */
  private $client;
  /** @var \Symfony\Component\Filesystem\LockHandler  */
  private $lockHandler;

  private $listenerConfig = [];

  public function __construct($appId, $appSecret, $region = 'eu-west-1', array $listenerConfig = [])
  {
    $this->client = SqsClient::factory([
      'key' => $appId,
      'secret' => $appSecret,
      'region' => $region
    ]);

    $this->lockHandler = new LockHandler($this->getTempFileName());

    $this->listenerConfig = $listenerConfig;
  }

  /**
   * @param $queueName
   * @param bool $alwaysListening
   */
  public function run($queueName, array $callback, $alwaysListening = false)
  {
    if($alwaysListening && !$this->setPermanentListener())
    {
      return;
    }

    $queueUrl = $this->getQueueUrl($queueName);
    $config = ['QueueUrl' => $queueUrl] + $this->listenerConfig;

    do
    {
      /** @var Guzzle\Service\Resource\Model $response */
      $response = $this->client->receiveMessage($config);
      $messages = $response['Messages'];
      foreach ($messages as $message) {
        call_user_func($callback, $message);
        $this->client->deleteMessage([
          'QueueUrl' => $queueUrl,
          'ReceiptHandle' => $message['ReceiptHandle']
        ]);
      }
    }while($alwaysListening);
  }

  protected function getQueueUrl($queueName)
  {
    if(filter_var($queueName,FILTER_VALIDATE_URL)) {
      return $queueName;
    }

    $result = $this->client->getQueueUrl([
      'QueueName' => $queueName
    ]);

    return $result['QueueUrl'];
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

  public function setConfig($key, $value)
  {
    if(null === $value && isset($this->listenerConfig[$key]))
    {
      unset($this->listenerConfig[$key]);
    }

    $this->listenerConfig[$key] = $value;
  }

  public function getConfig($key, $default = null)
  {
    return array_key_exists($key, $this->listenerConfig)? $this->listenerConfig[$key]: $default;
  }
}