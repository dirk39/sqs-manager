<?php


namespace Exception;

class ListenerAlreadyRunningException extends \RuntimeException
{
  protected $message = "listener already running";

  public function __construct($queueName = "")
  {
    if($queueName)
    {
      $this->message = $queueName.' '.$this->message;
    }

    parent::__construct($this->message);
  }

}