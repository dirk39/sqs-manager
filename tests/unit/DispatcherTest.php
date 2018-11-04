<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 01/11/18
 * Time: 20.12
 */

namespace test\unit;

use SQSManager\Exception\InvalidListenerException;
use SQSManager\Dispatcher\Dispatcher;
use SQSManager\MessageInterface;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
  public function testAddValidListener()
  {
    $dispatcher = new Dispatcher;

    $dispatcher->addListener('sqs_queue_1', function(MessageInterface $message){});

    $this->assertCount(1, $dispatcher->getListeners('sqs_queue_1'));
  }

  public function testAddInvalidListener()
  {
    $dispatcher = new Dispatcher;

    $this->expectException(InvalidListenerException::class);
    $this->expectExceptionMessage("Listener must be a callable function");

    $dispatcher->addListener('sqs_queue_1', 'invalid listener');
  }

}