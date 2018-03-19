<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 12/03/18
 * Time: 23.31
 */

namespace test\fake;


class FakeSQSListener extends \SQSListener
{

  public function execSetPermanentListener()
  {
    return $this->setPermanentListener();
  }

}