<?php declare(strict_types=1);
namespace PZBot\Commands;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Telegram;
use PZBot\Env;
use PZBot\Service\LogsParser\LogsParserFactory;

abstract class AbstractCommand extends SystemCommand
{
  protected ?User $user;
  protected Message $message;
  protected Env $appConfig;
  protected LogsParserFactory $logsParserFactory;

  function __construct(Telegram $telegram, ?Update $update = null)
  {
    parent::__construct($telegram, $update);

    $this->appConfig = new Env();
    $this->logsParserFactory = new LogsParserFactory;
  }
  
  public function preExecute(): ServerResponse
  {
    $this->user = $this->getMessage()->getFrom();
    $this->message = $this->getMessage();
    
    $this->createHook();

    return parent::preExecute();
  }

  function createHook(): void
  {}

  function getMessageText(): string
  {
    return $this->message->getText(true);
  }
}