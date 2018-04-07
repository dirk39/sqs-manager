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

class ManagerTest extends \PHPUnit_Framework_TestCase
{
  private static $missingCredentials = false;
  /** @var SqsClient */
  private static $client;
  private static $version = '2012-11-05';
  private static $queueName = 'dirk39_test_manager';
  private static $queueUrl = '';

  public static function setUpBeforeClass()
  {
    if(!defined('AWS_KEY') || !defined('AWS_SECRET') || !defined('AWS_REGION'))
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
      'QueueName' => self::$queueName
    ])['QueueUrl'];

    $messages = [];
    for($i = 0;$i < 15; $i++)
    {
      $messages[] = [
        'Id' => $i,
        'MessageBody' => \json_encode(['id' => '1', 'message' => 'ciao'])
      ];
    }
    foreach (array_chunk($messages, 10) as $chunked ) {
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
}
