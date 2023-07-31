<?php declare(strict_types=1);

namespace PZBot\Exceptions\Checked;
use PZBot\Exceptions\CheckedException;

class EventsNotFoundException extends CheckedException
{
  const MESSAGE = "Event '%s' was not found";

 public function __construct(string $eventName) 
 {
  $message = sprintf(self::MESSAGE, $eventName);

  parent::__construct($message);
 }
}