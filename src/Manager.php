<?php


use \Aws\Sqs\SqsClient;
use \Symfony\Component\Filesystem\LockHandler;

class Manager implements ManagerInterface
{
  private $client, $lockHandler;

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

  /**
   * @param $queueName
   * @return bool
   */
  protected function setPermanentListener($queueName)
  {
    $this->lockHandler = new LockHandler($this->getTempFileName($queueName));
    if($this->lockHandler->lock()) {
      return true;
    }

    return false;
  }

  /**
   * @param $queueName
   * @return string
   */
  protected function getTempFileName($queueName)
  {
    return sha1(__CLASS__.'_'.$queueName).'.lock';
  }


}