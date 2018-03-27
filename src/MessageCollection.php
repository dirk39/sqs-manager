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
  private $position = 0;

  public function __construct(array $messages, \Aws\Sqs\SqsClient $client)
  {
    $this->init($messages, $client);
    $this->position;
  }

  protected function init(array $messages, \Aws\Sqs\SqsClient $client)
  {
    foreach ($messages as $message)
    {
      $this->messages[] = new \Message($message, 'lol',$client);
    }
  }

  public function key()
  {
    return $this->position;
  }

  public function current()
  {
    return $this->messages[$this->position];
  }

  public function next()
  {
    ++$this->position;
  }

  public function rewind()
  {
    $this->position = 0;
  }

  public function valid()
  {
    return isset($this->messages[$this->position]);
  }
}