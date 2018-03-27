<?php

namespace unit;
use \test\fake\FakeListener;

class ListenerTest extends \PHPUnit_Framework_TestCase
{

  /** @test */
  public function testCheckOverlap()
  {
    $fakeListener = new FakeListener('appId','appSecret');
    $queueName = 'queueTest';
    $this->assertTrue($fakeListener->execSetPermanentListener($queueName), 'If not locked return true');

    $pid = pcntl_fork();
    if ($pid == -1) {
      die('zombie');
    } else if ($pid) {
      pcntl_waitpid($pid, $status); //Protect against Zombie children
    } else {
      $otherFakeListener = new FakeListener('appId','appSecret');
      $this->assertFalse($otherFakeListener->execSetPermanentListener($queueName),'If already running return false');
    }
  }

}
