<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 11/03/18
 * Time: 17.12
 */


class Test extends PHPUnit_Framework_TestCase
{

  public function testRun() {
    $fake = new \test\fake\FakeTask;
    $fake->runTrait();
  }
}
