<?php


use \Aws\Sqs\SqsClient;

class Manager implements ManagerInterface
{
  private $client;

  private $version = '2012-11-05';

  private $visibilityTimeout = 30, $maxNumberOfMessages = 1, $waitTimeSeconds;

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
    $this->maxNumberOfMessages = $number;

    return $this;
  }

  public function setVisibilityTimeout($seconds)
  {
    $this->visibilityTimeout = $seconds;

    return $this;
  }

  public function setWaitTimeSeconds($seconds)
  {
    $this->waitTimeSeconds = $seconds;

    return $this;
  }

  public function run($queueName, $callback, $keepAlive = false, $options = [])
  {

  }


}