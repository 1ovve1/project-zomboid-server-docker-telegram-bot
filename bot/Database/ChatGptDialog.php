<?php declare(strict_types=1);

namespace PZBot\Database;
use OpenAI\Responses\Chat\CreateResponseChoice;
use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;

class ChatGptDialog extends QueryBuilder implements MigrateAble
{
  const TOKEN_LENGTH = 4000;
  static function collectMessageHistoryFromUserId(int $userId, int $limit = 20): array
  {
    $queryBox = ChatGptDialog::select(["role", "content"])
      ->where(["user_id"], $userId)
      ->orderBy(["id"], false)
      ->limit($limit)
      ->save();

    $messageHistory = $queryBox->fetchAll();
      
    // TODO: this is really huge overhead i think
    $totalMessagesString = array_reduce(
      $messageHistory,
      fn($acc, $x) => $acc .= $x["content"],
      ''
    );
    
    if (strlen($totalMessagesString) >= self::TOKEN_LENGTH) {
      return self::collectMessageHistoryFromUserId($userId, $limit - 1);
    }

    return array_reverse($messageHistory);
  }

  static function addMessage(int $userId, string $userQuestion, CreateResponseChoice $botAnswer): void
  {
    ChatGptDialog::insert([
      "user_id" => $userId,
      "role" => "user",
      "content" => $userQuestion
    ])->save();

    $botMessage = $botAnswer->message;

    ChatGptDialog::insert([
      "user_id" => $userId,
      "role" => $botMessage->role,
      "content" => $botMessage->content
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
      ]
    ];
  }
}