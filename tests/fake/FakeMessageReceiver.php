<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 07/04/18
 * Time: 16.43
 */

namespace test\fake;


use Aws\Sqs\SqsClient;

class FakeMessageReceiver
{

  public function doNothing(\Message $message){}

  public function doThrowException(\Message $message)
  {
    throw new \Exception('YOU SHALL NOT PASS');
  }

  public function doDeleteMessage(\Message $message)
  {
    $client = new SqsClient([
      'credentials' => ['key' => AWS_KEY, 'secret' => AWS_SECRET],
      'region' => AWS_REGION,
      'version' => '2012-11-05'
    ]);

    $queueUrl = $client->getQueueUrl(['QueueName' => AWS_QUEUE_NAME])['QueueUrl'];
    $client->deleteMessage([
      'QueueUrl' => $queueUrl,
      'ReceiptHandle' => $message->getReceipt()
    ]);
  }

}
