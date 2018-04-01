<?php

namespace unit;
use \test\fake\FakeManager;

class ManagerTest extends \PHPUnit_Framework_TestCase
{

  /** @test */
  public function testCheckOverlap()
  {
    $fakeListener = new FakeManager('appId','appSecret', 'eu-west-1');
    $queueName = 'queueTest';
    $this->assertTrue($fakeListener->execSetPermanentListener($queueName), 'If not locked return true');

    $pid = pcntl_fork();
    if ($pid == -1) {
      die('zombie');
    } else if ($pid) {
      pcntl_waitpid($pid, $status); //Protect against Zombie children
    } else {
      $otherFakeListener = new FakeManager('appId','appSecret', 'eu-west-1');
      $this->expectException(\Exception\ListenerAlreadyRunningException::class);
      $otherFakeListener->execSetPermanentListener($queueName);
    }
  }

}
