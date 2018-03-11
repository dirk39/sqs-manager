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
    $fakeListener = new test\fake\FakeSQSListener('aaa','bbb');
    $this->assertFalse($fakeListener->execIsAlreadyRunning(), 'If not locked return false');

    $this->assertTrue($fakeListener->execIsAlreadyRunning(),'looool');
  }

}
