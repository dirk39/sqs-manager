<?php

namespace test\fake;


class FakeManager extends \Manager
{

  public function execSetPermanentListener($queueName)
  {
    return $this->setPermanentListener($queueName);
  }

}