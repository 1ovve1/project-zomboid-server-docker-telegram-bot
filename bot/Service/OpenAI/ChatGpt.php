<?php declare(strict_types=1);

namespace PZBot\Service\OpenAI;
use OpenAI;
use OpenAI\Client;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\Chat\CreateResponseChoice;
use PZBot\Database\ChatGptDialog;
use PZBot\Env;

class ChatGpt
{
  const SERVICE_ID = 9999;
  const MODEL_NAME = "gpt-3.5-turbo";
  protected Client $client;

  function __construct(string $apiKey) {
    $this->client = OpenAI::client($apiKey);
  }

  static function fromEnv(Env $config): self
  {
    return new self($config->get("BOT_CHATGPT_API_KEY"));
  }

  function answer(int $userId, string $question): CreateResponseChoice
  {
    ChatGptDialog::addMessageUser($userId, $question);

    $messageHistory = ChatGptDialog::collectMessageHistoryFromUserId($userId);

    $response = $this->createRequest($messageHistory, $question);

    $choice = $response->choices[0];

    ChatGptDialog::addMessageBot($userId, $choice);

    return $choice;
  }

  function answerWithoutUserId(string $question): CreateResponseChoice
  {
    return $this->answer(self::SERVICE_ID, $question);
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