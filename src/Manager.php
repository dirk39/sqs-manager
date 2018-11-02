<?php

namespace SQSManager;

use \Aws\Sqs\SqsClient;
use SQSManager\Exception\VisibilityTimeoutException;

class Manager implements ManagerInterface
{
  private $client;

  private $version = '2012-11-05';

  private $visibilityTimeout = 30, $maxNumberOfMessages = 1, $waitTimeSeconds;

  private $timeReceivedMessage;

  private $dispatcher;

  public function __construct($appId, $appSecret, $region, array $configs = [])
  {
    $configs = array_replace([
      'credentials' => ['key' => $appId, 'secret' => $appSecret],
      'region' => $region,
      'version' => $this->version
    ], $configs);

    $this->client = new SqsClient($configs);
  }

  public function setMaxNumberOfMessages($number)
  {
    if(!is_integer($number) || $number < 1 || $number > 10)
    {
      throw new \InvalidArgumentException('Number of messages must be an integer between 1 and 10');
    }
    $this->maxNumberOfMessages = (int)$number;

    return $this;
  }

  public function setVisibilityTimeout($seconds)
  {
    if(!is_integer($seconds) || $seconds < 0 || $seconds > (12*3600))
    {
      throw new \InvalidArgumentException('Visibility timeout must be an integer between 1 and 43200');
    }

    $this->visibilityTimeout = $seconds;

    return $this;
  }

  public function setWaitTimeSeconds($seconds)
  {
    if(!is_integer($seconds) || $seconds < 1 || $seconds > (20))
    {
      throw new \InvalidArgumentException('Wait time must be an integer between 1 and 20');
    }

    $this->waitTimeSeconds = $seconds;

    return $this;
  }

  public function run($queueName, $callback, array $listenerConfigs = [])
  {
    $queueUrl = $this->getQueueUrl($queueName);
    $configs = $this->prepareListenerConfigs($listenerConfigs + ['QueueUrl' => $queueUrl]);

    $response = $this->client->receiveMessage($configs);
    $messages = (array)$response['Messages'];

    $this->timeReceivedMessage = microtime(true);
    $messages = $this->prepareMessageCollection($messages);

    try
    {
      foreach ($messages as $key => $message) {
        $this->checkMessageTimedOut();

        call_user_func($callback, $message);

        $this->deleteMessage($queueUrl, $message);

      }
    }
    catch(\Exception $e) {
      $this->releaseAllMessages($queueUrl, $messages);
    }
  }

  public function setDispatcher(DispatcherInterface $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }


  /**
   * @param string $queueUrl
   * @param array $messages
   * @param integer $timeout
   */
  protected function changeVisibilityTimeout($queueUrl, array $messages, $timeout)
  {
    foreach (array_chunk($messages, 10) as $chunkedMessages) {
      $params = [
        'Entries' => array_map(function(Message $message) use ($timeout){
          return [
            'Id' => uniqid("id"),
            'ReceiptHandle' => $message->getReceipt(),
            'VisibilityTimeout' => $timeout,
          ];
        }, $chunkedMessages),
        'QueueUrl' => $queueUrl
      ];

      $result = $this->client->changeMessageVisibilityBatch($params);


    }
  }

  /**
   * @param string $queueUrl
   * @param array $messages
   */
  protected function releaseAllMessages($queueUrl, array $messages)
  {
    $this->changeVisibilityTimeout($queueUrl, $messages, 0);
  }

  protected function checkMessageTimedOut()
  {
    if(($this->timeReceivedMessage + $this->visibilityTimeout) <= microtime(true)) {
      throw new VisibilityTimeoutException;
    }
  }

  /**
   * @param array $messages
   * @return array
   */
  protected function prepareMessageCollection(array $messages)
  {
    $messageCollection = [];

    foreach ($messages as $message)
    {
      $messageCollection[] = new Message($message['MessageId'],$message['ReceiptHandle'],$message['MD5OfBody'],
        $message['Body'], $message['Attributes'], $message['MessageAttributes']);
    }

    return $messageCollection;
  }

  protected function deleteMessage($queueUrl, Message $message)
  {
    $this->client->deleteMessage([
      'QueueUrl' => $queueUrl,
      'ReceiptHandle' => $message->getReceipt()
    ]);
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

  /**
   * @param array $options
   *
   * @return array
   */
  protected function prepareListenerConfigs(array $options = [])
  {
    $defaultOptions = array_filter([
      'MaxNumberOfMessages' => $this->maxNumberOfMessages,
      'VisibilityTimeout' => $this->visibilityTimeout,
      'WaitTimeSeconds' => $this->waitTimeSeconds
    ], function($item){
      return !is_null($item);
    });

    return array_replace($defaultOptions, $options);
  }
}