<?php declare(strict_types=1);

namespace PZBot\Service\OpenAI;
use OpenAI;
use OpenAI\Client;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\Chat\CreateResponseChoice;
use PZBot\Database\ChatGptDialog;
use PZBot\Exceptions\Checked\ChatGptTokenSizeException;

class ChatGpt
{
  const DEFAULT_TOKEN_LENGHT = 4097;
  const DEFAULT_TOKEN_PER_MESSAGE_LENGTH = 500;
  const SERVICE_ID = 9999;
  const MODEL_NAME = "gpt-3.5-turbo";
  protected readonly Client $client;
  protected readonly Tokenizer $tokenizer;
  protected readonly int $tokenLimit;
  protected readonly int $tokenPerMessageLimit;

  function __construct(string $apiKey, int|string $tokenLimit, int|string $tokenPerMessageLimit) {

    $this->client = OpenAI::client($apiKey);
    $this->tokenizer = new Tokenizer();
    $this->tokenLimit = (int)$tokenLimit;
    $this->tokenPerMessageLimit = (int)$tokenPerMessageLimit;
  }

  static function fromEnv(): self
  {
    return new self(
      env("BOT_CHATGPT_API_KEY"),
      env("BOT_CHATGPT_TOKEN_LENGTH", self::DEFAULT_TOKEN_LENGHT),
      env("BOT_CHATGPT_USER_MSG_LENGTH", self::DEFAULT_TOKEN_PER_MESSAGE_LENGTH),
    );
  }

  /**
   * Generate string answer from ChatGPT
   * 
   * @param integer $userId
   * @param string $question
   * @param boolean $memory
   * @return string
   */
  function answer(int $userId, string $question, bool $memory = true, bool $dan = false): string
  {
    $questionTokenSize = $this->tokenizer->tokenCount($question);

    if ($questionTokenSize > $this->tokenLimit) {
      throw new ChatGptTokenSizeException($this->tokenLimit, $questionTokenSize);
    }

    $message = ['role' => 'user', 'content' => $question];
    $messageCollection = [];
    
    if ($memory) {
      $messageHistory = $this->getMessageHistoryFrom($userId, $questionTokenSize, $dan);

      $messageCollection = [
        ...$messageHistory,
        $message
      ];

      if ($dan) {
        $boilerplate = file_get_contents(BOT_DIR . '/Service/OpenAI/dan_boilerplate_russian.txt');

        $danExistsInMessageCollection = false;

        foreach ($messageCollection as $message) {
          if(str_contains($message['content'], $boilerplate)) {
            $danExistsInMessageCollection = true;
          }
        }

        if (false === $danExistsInMessageCollection) {
          return $this->answer($userId, $boilerplate . $question, $memory, $dan);
        }
      }
    } else {
      $messageCollection = [
        $message
      ];
    }
    
    var_dump($messageCollection);

    $response = $this->createRequest($messageCollection, $question);
            
    $botAnswerArray = $this->parseResponse($response);
    if ($dan) {
      var_dump($botAnswerArray['content']);
      preg_match('/(?<=\(Ответ в режиме Developer Mode\))\s*(.*?[\n.А-я ?!A-z]*)(?=(Normal Output|$))/', $botAnswerArray['content'], $mathces);
      
      if (is_null($mathces[1] ?? null)) {
        preg_match('/(?<=\(Developer Mode Output\))\s*(.*?[\n.А-я ?!A-z]*)(?=(Normal Output|$))/', $botAnswerArray['content'], $mathces);
      }

      $botAnswerArray['content'] = $mathces[1] ?? $botAnswerArray['content'];
    }

    ChatGptDialog::addMessageUser($userId, $question, $questionTokenSize, $dan);
    ChatGptDialog::addMessageBot($userId, $botAnswerArray['content'], $botAnswerArray['role'], $botAnswerArray['token_size'], $dan);

    return $botAnswerArray['content'];
  }

  /**
   * Give answer withour user id
   *
   * @param string $question
   * @return string
   */
  function answerWithoutUserId(string $question): string
  {
    return $this->answer(self::SERVICE_ID, $question, true);
  }

  /**
   * Give answer withour user id and memory
   *
   * @param string $question
   * @return string
   */
  function answerWithoutUserIdAndMemory(string $question): string
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

  /**
   * Collect messages from the history
   *
   * @param integer $userId
   * @param integer $startTokenSize
   * @param boolean $onlyDan
   * @return array
   */
  protected function getMessageHistoryFrom(int $userId, int $startTokenSize, bool $onlyDan = false): array
  {
    $collection = [];
    $history = ChatGptDialog::collectMessageHistoryFromUserId($userId, $onlyDan);

    $totalTokenSize = $startTokenSize;
    foreach($history as $messageFromHistory) {
      $messageHistoryTokenSize = $messageFromHistory['token_size'];
      $totalTokenSize += $messageHistoryTokenSize;
      
      if ($totalTokenSize > $this->tokenLimit) {
        break;
      }

      $collection[] = ['role' => $messageFromHistory['role'], 'content' => $messageFromHistory['content']];
    }

    return array_reverse($collection);
  }

  protected function parseResponse(CreateResponse $response): array
  {
    $botChoice = $response->choices[0];
    $botAnswerMessage = $botChoice->message->content;
    $botRole = $botChoice->message->role;
    $botAnswerTokenSize = $this->tokenizer->tokenCount($botAnswerMessage);

    return [
      'content' => $botAnswerMessage,
      'role' => $botRole,
      'token_size' => $botAnswerTokenSize,
    ];
  }
}