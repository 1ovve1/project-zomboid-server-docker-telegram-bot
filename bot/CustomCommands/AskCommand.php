<?php

namespace PZBot\CustomCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use PZBot\Commands\AbstractCommand;
use PZBot\Service\Chat\ChatGpt;

class AskCommand extends AbstractCommand
{
  /**
   * @var string
   */
  protected $name = 'ask';

  /**
   * @var string
   */
  protected $description = 'Ask bot';

  /**
   * @var string
   */
  protected $usage = '/ask';

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
      $this->chatGpt = new ChatGpt(
        $this->appConfig->get("BOT_CHATGPT_KEY")
      );
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

    $choice = $this->chatGpt->answer($this->user->getId(), $question);

    return $this->replyToChat($choice->message->content, ["reply_to_message_id" => $this->message->getMessageId()]);
  }
}
