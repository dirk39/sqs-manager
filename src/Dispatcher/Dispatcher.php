<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 02/11/18
 * Time: 8.21
 */

namespace SQSManager\Dispatcher;

class Dispatcher implements DispatcherInterface
{
  private $listeners = [];

  public function dispatch($queueName)
  {
    // TODO: Implement dispatch() method.
  }

  public function addListener($queueName, $listener)
  {
    if(!is_callable($listener)) {
      throw new \SQSManager\Exception\InvalidListenerException();
    }

    $this->listeners[$queueName][] = $listener;
  }

  /**
   * @param string $queueName
   * @return array
   */
  public function getListeners($queueName)
  {
    return isset($this->listeners[$queueName])? $this->listeners[$queueName]: [];
  }
}