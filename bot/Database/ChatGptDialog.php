<?php declare(strict_types=1);

namespace PZBot\Database;
use OpenAI\Responses\Chat\CreateResponseChoice;
use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;

class ChatGptDialog extends QueryBuilder implements MigrateAble
{
  const DEFAULT_TOKEN_SIZE = 4097;
  
  static function collectMessageHistoryFromUserId(int $userId, bool $dan = false): array
  {
    $queryBox = ChatGptDialog::select()
      ->where(["user_id"], $userId)
      ->andWhere(["dan"], $dan)
      ->orderBy(["id"], false)
      ->limit(50)
      ->save();

    $messageHistory = $queryBox->fetchAll();

    return $messageHistory;
  }

  static function addMessageUser(int $userId, string $userAnswer, int $tokenSize, bool $dan = false): void
  {
    self::addMessage($userId, $userAnswer, "user",   $tokenSize, $dan);
  }

  static function addMessageBot(int $userId, string $botAnswer, string $botRole, int $tokenSize, bool $dan = false): void
  {
    self::addMessage($userId, $botAnswer, $botRole, $tokenSize, $dan);
  }

  static function addMessage(int $userId, string $content, string $role, int $tokenSize, bool $dan = false): void
  {
    ChatGptDialog::insert([
      "user_id" => $userId,
      "role" => $role,
      "content" => $content,
      "token_size" => $tokenSize,
      "dan" => $dan
    ])->save();
  }

  static function migrationParams(): array
  {
    return [
      'fields' => [
        'id' => "BIGINT PRIMARY KEY AUTO_INCREMENT",
        'user_id' => "BIGINT UNSIGNED NOT NULL",
        "role" => "CHAR(10) NOT NULL",
        "content" => "TEXT NOT NULL",
        "token_size" => "INT NOT NULL",
        "dan" => "BOOLEAN NOT NULL DEFAULT false"
      ]
    ];
  }
}