<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 24/03/18
 * Time: 16.01
 */

class MessageCollection implements Iterator
{
  private $messages = [];

  public function __construct(array $messages, \Aws\Sqs\SqsClient $client)
  {
    $this->init($messages, $client);
  }

  protected function init(array $messages, \Aws\Sqs\SqsClient $client)
  {
    foreach ($messages as $message)
    {
      $this->messages[] = new \Message($message, $client);
    }
  }

  public function key()
  {
    return key($this->messages);
  }

  public function current()
  {
    return current($this->messages);
  }

  public function next()
  {
    return next($this->messages);
  }

  public function rewind()
  {
    return reset($this->messages);
  }

  public function valid()
  {
    return isset($this->messages[$this->key()]);
  }
}