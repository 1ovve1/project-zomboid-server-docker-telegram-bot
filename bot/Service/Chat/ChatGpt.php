<?php declare(strict_types=1);

namespace PZBot\Service\Chat;
use OpenAI;
use OpenAI\Client;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\Chat\CreateResponseChoice;
use PZBot\Database\ChatGptDialog;

class ChatGpt
{
  const MODEL_NAME = "gpt-3.5-turbo";
  protected Client $client;

  function __construct(string $apiKey) {
    $this->client = OpenAI::client($apiKey);
  }

  function answer(int $userId, string $question): CreateResponseChoice
  {
    $messageHistory = ChatGptDialog::collectMessageHistoryFromUserId($userId);

    $response = $this->createRequest($messageHistory, $question);

    $choice = $response->choices[0];

    ChatGptDialog::addMessage($userId, $question, $choice);

    return $choice;
  }

  protected function createRequest(array $messageHistory, string $question): CreateResponse
  {
    return $this->client->chat()->create([
      'model' => self::MODEL_NAME,
      'messages' => [
          ...$messageHistory,
          ["role" => "user", "content" => $question],
      ],
    ]);
  }
}