<?php declare(strict_types=1);

namespace PZBot\Events;

enum EventsEnum: string 
{
  case BEFORE_HANDLE_UPDATES = "BEFORE_HANDLE_UPDATES";
  case AFTER_HANDLE_UPDATES = "AFTER_HANDLE_UPDATES";
  case AFTER_HANDLE_RESPONSE = "AFTER_HANDLE_RESPONSE";
}