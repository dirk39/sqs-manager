<?php

use Aws\Sqs\SqsClient;

class SQSListener
{
  /** @var Aws\Sqs\SqsClient */
  private $client;

  public function __construct($appId, $appSecret, $region = 'eu-west-1')
  {
    $this->client = SqsClient::factory([
      'key' => $appId,
      'secret' => $appSecret,
      'region' => $region
    ]);
  }

  public function run($queueName, $infiniteLoop = false, $options = [])
  {
    if($infiniteLoop && $this->isAlreadyRunning())
    {
      $this->isAlreadyRunning();
    }
  }

  protected function isAlreadyRunning()
  {
    $filename = sys_get_temp_dir().DIRECTORY_SEPARATOR.sha1(__CLASS__).'.lock';
    $fh = fopen($filename, file_exists($filename)?'r':'x+');

    $wouldblock = null;
    if(!flock($fh, LOCK_EX|LOCK_NB, $wouldblock))
    {
      if($wouldblock === 1)
      {
        return true;
      }

      throw new Exception\MissingWritePermissionException;
    }

    return false;
  }

}