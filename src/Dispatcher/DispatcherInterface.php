<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 02/11/18
 * Time: 8.16
 */

namespace SQSManager\Dispatcher;

interface DispatcherInterface
{
  public function dispatch($queueName);

  public function addListener($queueName, $listener);

  /**
   * @param string $queueName
   * @return array
   */
  public function getListeners($queueName);
}