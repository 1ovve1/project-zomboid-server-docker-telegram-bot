<?php

namespace PZBot\CustomCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use PZBot\Commands\AbstractCommand;
use PZBot\Service\OpenAI\ChatGpt;

class DanCommand extends AbstractCommand
{
  /**
   * @var string
   */
  protected $name = 'dan';

  /**
   * @var string
   */
  protected $description = 'Ask chat gpt as dan';

  /**
   * @var string
   */
  protected $usage = '/dan';

  /**
   * @var string
   */
  protected $version = '1.0';

  /**
   * @var bool
   */
  protected $private_only = false;

  /**
   * @var ChatGpt - service
   */
  protected ChatGpt $chatGpt;

  function createHook(): void
  {
    parent::createHook();
    
    $this->chatGpt = ChatGpt::fromEnv();
  }

  /**
   * Main command execution
   *
   * @return ServerResponse
   * @throws TelegramException
   */
  public function execute(): ServerResponse
  {
    $question = $this->getMessageText();

    $answer = $this->chatGpt->answer($this->user->getId(), $question, true, true);

    return $this->replyToChat($answer, ["reply_to_message_id" => $this->message->getMessageId()]);
  }
}
