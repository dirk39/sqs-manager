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

  public function testSetVisibilityTimeoutNotInt()
  {
    $manager = new \Manager('appId', 'AppSecret', 'eu-west-1');
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage("Visibility timeout must be an integer between 1 and 43200");
    $manager->setVisibilityTimeout(1.10);
  }

  public function testSetVisibilityTimeoutOverUpperLimit()
  {
    $manager = new \Manager('appId', 'AppSecret', 'eu-west-1');
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage("Visibility timeout must be an integer between 1 and 43200");
    $manager->setVisibilityTimeout(86400);
  }

  public function testSetVisibilityTimeoutUnderLowerLimit()
  {
    $manager = new \Manager('appId', 'AppSecret', 'eu-west-1');
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage("Visibility timeout must be an integer between 1 and 43200");
    $manager->setVisibilityTimeout(-1);
  }


  public function testSetMaxNumberOfMessagesNotInt()
  {
    $manager = new \Manager('appId', 'AppSecret', 'eu-west-1');
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage("Number of messages must be an integer between 1 and 10");
    $manager->setMaxNumberOfMessages(1.10);
  }

  public function testSetMaxNumberOfMessagesOverUpperLimit()
  {
    $manager = new \Manager('appId', 'AppSecret', 'eu-west-1');
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage("Number of messages must be an integer between 1 and 10");
    $manager->setMaxNumberOfMessages(20);
  }

  public function testSetMaxNumberOfMessagesUnderLowerLimit()
  {
    $manager = new \Manager('appId', 'AppSecret', 'eu-west-1');
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage("Number of messages must be an integer between 1 and 10");
    $manager->setMaxNumberOfMessages(0);
  }


  public function testSetWaitTimeSecondsNotInt()
  {
    $manager = new \Manager('appId', 'AppSecret', 'eu-west-1');
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage("Wait time must be an integer between 1 and 20");
    $manager->setWaitTimeSeconds(1.10);
  }

  public function testSetWaitTimeSecondsOverUpperLimit()
  {
    $manager = new \Manager('appId', 'AppSecret', 'eu-west-1');
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage("Wait time must be an integer between 1 and 20");
    $manager->setWaitTimeSeconds(50);
  }

  public function testSetWaitTimeSecondsUnderLowerLimit()
  {
    $manager = new \Manager('appId', 'AppSecret', 'eu-west-1');
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage("Wait time must be an integer between 1 and 20");
    $manager->setWaitTimeSeconds(0);
  }

}
