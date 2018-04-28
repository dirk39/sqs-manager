<?php

namespace test\fake;
use SQSManager\Manager;


class FakeManager extends Manager
{

  public function execSetPermanentListener($queueName)
  {
    return $this->setPermanentListener($queueName);
  }

}