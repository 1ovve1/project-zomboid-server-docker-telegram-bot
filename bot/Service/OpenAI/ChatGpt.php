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
  const DEFAULT_TOKEN_LENGHT = 4097;
  const DEFAULT_TOKEN_PER_MESSAGE_LENGTH = 500;
  const SERVICE_ID = 9999;
  const MODEL_NAME = "gpt-3.5-turbo";
  protected readonly Client $client;
  protected readonly int $tokenLimit;
  protected readonly int $tokenPerMessageLimit;

  function __construct(string $apiKey, int|string $tokenLimit, int|string $tokenPerMessageLimit) {
    $this->client = OpenAI::client($apiKey);
    $this->tokenLimit = (int)$tokenLimit;
    $this->tokenPerMessageLimit = (int)$tokenPerMessageLimit;
  }

  static function fromEnv(Env $config): self
  {
    return new self(
      $config->get("BOT_CHATGPT_API_KEY"),
      $config->get("BOT_CHATGPT_TOKEN_LENGTH", self::DEFAULT_TOKEN_LENGHT),
      $config->get("BOT_CHATGPT_USER_MSG_LENGTH", self::DEFAULT_TOKEN_PER_MESSAGE_LENGTH),
    );
  }

  function answer(int $userId, string $question, bool $memory = true): CreateResponseChoice
  {
    $message = ['role' => 'user', 'content' => $question];
    $messageCollection = [$message];
    
    $limit = 0;
    while($memory && $limit < 20) {
      $history = ChatGptDialog::collectMessageHistoryFromUserId($userId, $limit);

      $tmpMessageCollection = array_merge([ $message ], $history);

      $totalMessagesString = array_reduce(
        $tmpMessageCollection,
        fn($acc, $x) => $acc .= $x["content"],
        ''
      );

      if (strlen($totalMessagesString) < $this->tokenLimit) {
        break;
      }

      $messageCollection = $tmpMessageCollection;
      $limit--;
    }

    
    
    $response = $this->createRequest($messageCollection, $question);
    
    
    $choice = $response->choices[0];
    
    ChatGptDialog::addMessageUser($userId, $question);

    ChatGptDialog::addMessageBot($userId, $choice);

    return $choice;
  }

  function answerWithoutUserId(string $question): CreateResponseChoice
  {
    return $this->answer(self::SERVICE_ID, $question, false);
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