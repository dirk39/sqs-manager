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

  public function addListener($queueName, $listener, $priority = 0)
  {
    if(!is_callable($listener)) {
      throw new \SQSManager\Exception\InvalidListenerException();
    }

    $this->listeners[$queueName][] = ['priority' => $priority, 'listener' => $listener];
    usort($this->listeners[$queueName], function ($a, $b){
      if( $a['priority'] == $b['priority']) {
        return 0;
      }

      if( $a['priority'] > $b['priority']) {
        return 1;
      }

      return -1;
    });
  }

  /**
   * @param string $queueName
   * @return array
   */
  public function getListeners($queueName)
  {
    if(!isset($this->listeners[$queueName])) {
      return [];
    }

    return array_column($this->listeners[$queueName],'listener');
  }
}