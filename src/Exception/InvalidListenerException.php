<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 04/11/18
 * Time: 12.24
 */

namespace SQSManager\Exception;

use Throwable;

class InvalidListenerException extends \InvalidArgumentException
{
  protected $message = 'Listener must be a callable function';

  public function __construct($message = "", $code = 0, Throwable $previous = null)
  {
    $message = $message === 'queue_name'? 'Queue name must be a string':
      $this->message;

    parent::__construct($message, $code, $previous);
  }
}