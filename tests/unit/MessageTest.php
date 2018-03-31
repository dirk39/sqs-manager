<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 31/03/18
 * Time: 11.37
 */

namespace test\unit;

use \Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
  public function testInvalidBody()
  {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Invalid body');

    $message = new Message('11111','11111', md5('valido'), 'nonvalido');
  }
}
