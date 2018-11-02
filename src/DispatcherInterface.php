<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 02/11/18
 * Time: 8.16
 */

namespace SQSManager;

interface DispatcherInterface
{
  public function dispatch($name);

  public function addListener($name, $listener);
}