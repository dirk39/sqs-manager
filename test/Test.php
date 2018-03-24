<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 11/03/18
 * Time: 17.12
 */


class Test extends PHPUnit_Framework_TestCase
{

  public function testCheckOverlap()
  {
    $fakeListener = new test\fake\FakeListener('aaa','bbb');
    $this->assertTrue($fakeListener->execSetPermanentListener(), 'If not locked return false');

    $pid = pcntl_fork();
    if ($pid == -1) {
      die('zombie');
    } else if ($pid) {
      pcntl_waitpid($pid, $status); //Protect against Zombie children
    } else {
      $otherFakeListener = new test\fake\FakeListener('aaa','bbb');
      $this->assertFalse($otherFakeListener->execSetPermanentListener(),'looool');
    }
  }

}
