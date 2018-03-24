<?php

use Aws\Sqs\SqsClient;
use \Symfony\Component\Filesystem\LockHandler;

class Listener
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
   * @param mixed $callback
   * @param bool $alwaysListening
   * @param array $options
   */
  public function run($queueName, $callback, $alwaysListening = false, array $options = [])
  {
    if($alwaysListening && !$this->setPermanentListener())
    {
      return;
    }

    $queueUrl = $this->getQueueUrl($queueName);
    $config = array_replace(['QueueUrl' => $queueUrl] + $this->listenerConfig, $options);

    do
    {
      /** @var Guzzle\Service\Resource\Model $response */
      $response = $this->client->receiveMessage($config);
      $messages = (array)$response['Messages'];
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
    if($this->lockHandler->lock()) {
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
}