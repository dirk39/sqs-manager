<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 12/03/18
 * Time: 23.31
 */

namespace test\fake;


class FakeListener extends \Listener
{

  public function execSetPermanentListener($queueName)
  {
    return $this->setPermanentListener($queueName);
  }

}