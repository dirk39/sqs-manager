<?php

namespace test\fake;

class FakeTask
{
  use \SQSWorkerTrait;

  public function runTrait() {
    $this->run();
  }


  public function doSomething() {

    return 1;
  }

}