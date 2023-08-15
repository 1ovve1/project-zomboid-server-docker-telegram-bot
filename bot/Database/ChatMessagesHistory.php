<?php declare(strict_types=1);

namespace PZBot\Database;
use PZBot\Commands\AbstractCommand;
use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;

class ChatMessagesHistory extends QueryBuilder implements MigrateAble
{
  static function getRecord(AbstractCommand $command, int $chatId): array
  {
    return self::select()
        ->where(['command_class'], $command::class)
        ->andWhere(['chat_id'], $chatId)
        ->save()
        ->fetchAll();
  }

  static function updateMessageHistory(AbstractCommand $command, int $chatId, int $userId, int $userMessageId, int $botMessageId)
  {
    if (self::isIfMessageExists($command, $chatId)) {
      self::updateRecord($command, $chatId, $userId, $userMessageId, $botMessageId);
    } else {
      self::createRecord($command, $chatId, $userId, $userMessageId, $botMessageId);
    }

  }

  static function isIfMessageExists(AbstractCommand $command, int $chatId): bool
  {
    $queryResult = self::select()
      ->where('command_class', $command::class)
      ->andWhere('chat_id', $chatId)
      ->limit(1)
      ->save();

    return $queryResult->isNotEmpty();
  }

  static function updateRecord(AbstractCommand $command, int $chatId, int $userId, int $userMessageId, int $botMessageId): void
  {
    self::update('user_message_id', $userMessageId)
      ->where(['chat_id'], $chatId)
      ->andWhere(['user_id'], $userId)
      ->andWhere(['command_class'], $command::class)
      ->save();

    self::update('bot_message_id', $botMessageId)
      ->where(['chat_id'], $chatId)
      ->andWhere(['user_id'], $userId)
      ->andWhere(['command_class'], $command::class)
      ->save();
  }

  static function createRecord(AbstractCommand $command, int $chatId, int $userId, int $userMessageId, int $botMessageId): void
  {
    self::insert([
      'command_class' => $command::class,
      'chat_id' => $chatId,
      'user_id' => $userId,
      'user_message_id' => $userMessageId,
      'bot_message_id' => $botMessageId
    ])->save();
  }

  static function migrationParams(): array
  {
    return [
      'fields' => [
        'id' => 'BIGINT PRIMARY KEY AUTO_INCREMENT',
        
        'command_class' => 'CHAR(255) NOT NULL',
        
        'chat_id' => 'BIGINT NOT NULL',
        'user_id' => 'BIGINT NOT NULL',
        
        'user_message_id' => 'BIGINT NOT NULL',
        'bot_message_id' => 'BIGINT NOT NULL',
      ]
    ];
  }
}