<?php

use Aws\Sqs\SqsClient;

class Listener
{
  /** @var Aws\Sqs\SqsClient */
  private $client;

  private $listenerConfig = [];

  public function __construct($appId, $appSecret, $region = 'eu-west-1', array $listenerConfig = [])
  {
    $this->client = new SqsClient([
      'credentials' => ['key' => $appId, 'secret' => $appSecret],
      'region' => $region,
      'version' => '2012-11-05'
    ]);

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
    if($alwaysListening && !$this->setPermanentListener($queueName))
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
      if($messages)
      {
        $messageCollection = new MessageCollection($messages);
      }
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

  public function setConfig($key, $value)
  {
    if(null === $value && isset($this->listenerConfig[$key]))
    {
      unset($this->listenerConfig[$key]);
    }

    $this->listenerConfig[$key] = $value;
  }
}
