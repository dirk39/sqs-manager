<?php

namespace Exception;


class VisibilityTimeoutException extends \RuntimeException
{
  protected $message = 'Visibility timeout of received messages is expired.';
}