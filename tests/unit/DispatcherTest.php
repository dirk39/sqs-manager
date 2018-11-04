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
use SQSManager\Message;
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

  public function testRetrieveListenersSortByPriority()
  {
    $dispatcher = new Dispatcher;
    $first = function(MessageInterface $message){ echo 'first'; };
    $second = function(MessageInterface $message){ echo 'second'; };
    $third = function(MessageInterface $message){ echo 'third'; };

    $dispatcher->addListener('sqs_queue_1', $first, -1);
    $dispatcher->addListener('sqs_queue_1', $third, 10);
    $dispatcher->addListener('sqs_queue_1', $second, 2);

    $this->assertTrue($this->arrays_are_similar(
      [$first, $second, $third],
      $dispatcher->getListeners('sqs_queue_1')
    ));
  }

  public function testDispatchEvent()
  {
    $dispatcher = new Dispatcher;
    $checker = new \stdClass;
    $checker->ran = false;

    $dispatcher->addListener('sqs_queue_1', function(MessageInterface $message) use($checker){
      $checker->ran = true;
    });

    $message = new Message('1','awsa',md5("test"), "test");
    $dispatcher->dispatch('sqs_queue_1', $message);

    $this->assertTrue($checker->ran);
  }

  private function arrays_are_similar($a, $b)
  {
    // we know that the indexes, but maybe not values, match.
    // compare the values between the two arrays
    foreach($a as $k => $v) {
      if ($v !== $b[$k]) {
        return false;
      }
    }
    // we have identical indexes, and no unequal values
    return true;
  }

}