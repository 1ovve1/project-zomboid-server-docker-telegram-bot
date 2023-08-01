<?php declare(strict_types=1);

namespace PZBot\Helpers;

use Longman\TelegramBot\Request;

class TelegramRequestHelper
{
  static function sendImageToAllGroups(string $imagePath, string $caption): void
  {
    Request::sendToActiveChats(
      'sendPhoto',
      [
        "photo" => Request::encodeFile($imagePath),
        "caption" => $caption
      ],
      [
        'groups'      => true,
        'supergroups' => true,
        'channels'    => false,
        'users'       => true,
      ]
    );
  }

  static function sendMessageToAllGroups(string $message): void
  {
    Request::sendToActiveChats(
      'sendMessage',
      [
        "text" => $message
      ],
      [
        'groups'      => true,
        'supergroups' => true,
        'channels'    => false,
        'users'       => true,
      ]
    );
  }
}