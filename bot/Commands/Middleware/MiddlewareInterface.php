<?php declare(strict_types=1);
namespace PZBot\Commands\Middleware;

use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use PZBot\Commands\AbstractCommand;

interface MiddlewareInterface
{
  function before(Message $message, AbstractCommand $command): void;
  function after(ServerResponse $response, Message $message, AbstractCommand $command): ServerResponse;
}