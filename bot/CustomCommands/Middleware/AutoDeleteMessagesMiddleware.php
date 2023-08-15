<?php declare(strict_types=1);

namespace PZBot\CustomCommands\Middleware;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use PZBot\Commands\AbstractCommand;
use PZBot\Commands\Middleware\MiddlewareInterface;
use PZBot\Database\ChatMessagesHistory;
use PZBot\Helpers\TelegramRequestHelper;

class AutoDeleteMessagesMiddleware implements MiddlewareInterface
{
  function before(Message $message, AbstractCommand $command): void
  {
    $chatId = $message->getChat()->getId();

    $history = ChatMessagesHistory::getRecord($command, $chatId);

    foreach ($history as $message) {
        TelegramRequestHelper::deleteMessage($message['chat_id'], $message['user_message_id']);
        TelegramRequestHelper::deleteMessage($message['chat_id'], $message['bot_message_id']);
    }
  }

  function after(ServerResponse $response, Message $message, AbstractCommand $command): ServerResponse
  {
    if ($response->isOk()) {
      $chatId = $message->getChat()->getId();
      $userMessageId = $message->getMessageId();
      
      $botMessageId = $response->getResult()->getMessageId();

      ChatMessagesHistory::updateMessageHistory(
        $command, 
        $chatId, 
        $userMessageId, 
        $botMessageId
      );
    }

    return $response;
  }
}