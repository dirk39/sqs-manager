<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 07/04/18
 * Time: 15.28
 */

namespace test\integration;
use \Aws\Sqs\SqsClient;

use Manager;
use test\fake\FakeMessageReceiver;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
  private static $missingCredentials = false;
  private static $messages = [];
  /** @var SqsClient */
  private static $client;
  private static $version = '2012-11-05';
  private static $queueUrl = '';

  public static function setUpBeforeClass()
  {
    if(!defined('AWS_KEY') || !defined('AWS_SECRET') || !defined('AWS_REGION') || !defined('AWS_QUEUE_NAME'))
    {
      self::$missingCredentials = true;
      return;
    }

    self::$client = new SqsClient([
      'credentials' => ['key' => AWS_KEY, 'secret' => AWS_SECRET],
      'region' => AWS_REGION,
      'version' => self::$version
    ]);

    self::$queueUrl = self::$client->createQueue([
      'QueueName' => AWS_QUEUE_NAME
    ])['QueueUrl'];

    for($i = 0;$i < 15; $i++)
    {
      self::$messages[] = [
        'Id' => $i,
        'MessageBody' => \json_encode(['id' => $i, 'message' => 'ciao'])
      ];
    }

    foreach (array_chunk(self::$messages, 10) as $chunked ) {
      self::$client->sendMessageBatch([
        'QueueUrl' => self::$queueUrl,
        'Entries' => $chunked
      ]);
    }
  }

  protected function setUp()
  {
    if(self::$missingCredentials)
    {
      $this->markTestSkipped("Missing config.php file with constants 'AWS_KEY', 'AWS_SECRET' and 'AWS_REGION'");
      return;
    }
  }

  public static function tearDownAfterClass()
  {
    if(!self::$client instanceof SqsClient)
    {
      return;
    }

    try
    {
      self::$client->deleteQueue(['QueueUrl' => self::$queueUrl]);
    }
    catch (\Exception $e) {}
  }

  public function testManageMessagesAndDeleteThem()
  {
    $queueAttributesBeforeTest = $this->getQueueAttributes();
    $manager = new \Manager(AWS_KEY, AWS_SECRET, AWS_REGION);
    $messageReceiver = new FakeMessageReceiver();
    $manager->setMaxNumberOfMessages(10);


    $manager->run(AWS_QUEUE_NAME, [$messageReceiver, 'doNothing']);


    /* SQS have a eventual consistency read so i wait a while before update stats*/
    sleep('20');
    $queueAttributesAfterTest = $this->getQueueAttributes();
    $this->assertLessThan(
      $queueAttributesBeforeTest['ApproximateNumberOfMessages'],
      $queueAttributesAfterTest['ApproximateNumberOfMessages']
    );
  }

  public function testReleaseAllMessagesIfExceptionThrown()
  {
    $queueAttributesBeforeTest = $this->getQueueAttributes();
    $manager = new \Manager(AWS_KEY, AWS_SECRET, AWS_REGION);
    $messageReceiver = new FakeMessageReceiver();
    $manager->setMaxNumberOfMessages(10);


    try
    {
      $manager->run(AWS_QUEUE_NAME, [$messageReceiver, 'doThrowException']);
    }catch (\Exception $e) {}


    sleep('20');
    $queueAttributesAfterTest = $this->getQueueAttributes();
    $this->assertEquals(
      $queueAttributesBeforeTest['ApproximateNumberOfMessages'],
      $queueAttributesAfterTest['ApproximateNumberOfMessages']
    );
  }

  public function testDeleteUsedMessagesAlreadyDeleted()
  {
    $queueAttributesBeforeTest = $this->getQueueAttributes();
    $manager = new \Manager(AWS_KEY, AWS_SECRET, AWS_REGION);
    $messageReceiver = new FakeMessageReceiver();
    $manager->setMaxNumberOfMessages(10);


    $manager->run(AWS_QUEUE_NAME, [$messageReceiver, 'doDeleteMessage']);


    /* SQS have a eventual consistency read so i wait a while before update stats*/
    sleep('20');
    $queueAttributesAfterTest = $this->getQueueAttributes();
    $this->assertLessThan(
      $queueAttributesBeforeTest['ApproximateNumberOfMessages'],
      $queueAttributesAfterTest['ApproximateNumberOfMessages']
    );
  }

  private function getQueueAttributes()
  {
    return self::$client->getQueueAttributes([
      'AttributeNames' => ['All'],
      'QueueUrl' => self::$queueUrl
    ])['Attributes'];
  }
}
