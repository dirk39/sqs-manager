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

  /**
   * @param string $queueName
   * @param callable $listener
   * @param int $priority
   * @return array
   */
  public function addListener($queueName, $listener, $priority = 0);

  /**
   * @param string $queueName
   * @return array
   */
  public function getListeners($queueName);
}