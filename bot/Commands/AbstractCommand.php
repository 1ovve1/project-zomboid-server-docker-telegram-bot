<?php declare(strict_types=1);
namespace PZBot\Commands;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Telegram;
use PZBot\Commands\Middleware\AbstractMiddleware;
use PZBot\Commands\Middleware\MiddlewareInterface;
use PZBot\Env;
use PZBot\Service\LogsParser\LogsParserFactory;

abstract class AbstractCommand extends SystemCommand
{
  /**
   * Request user entity
   *
   * @var User|null
   */
  protected ?User $user;
  /**
   * Request message entity
   *
   * @var Message
   */
  protected Message $message;
  /**
   * Application config
   *
   * @var Env
   */
  protected Env $appConfig;
  /**
   * Logs parser factory instance
   *
   * @var LogsParserFactory
   */
  protected LogsParserFactory $logsParserFactory;

  function __construct(Telegram $telegram, ?Update $update = null)
  {
    parent::__construct($telegram, $update);

    $this->appConfig = new Env();
    $this->logsParserFactory = new LogsParserFactory;
  }
  
  function preExecute(): ServerResponse
  {    
    $this->createHook();

    $this->handleBeforeMiddleware();
    
    $response = parent::preExecute();

    return $this->handleAfterMiddleware($response);
  }

  /**
   * Default create hook
   *
   * @return void
   */
  function createHook(): void
  {
    $this->user = $this->getMessage()->getFrom();
    $this->message = $this->getMessage();
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  private function handleBeforeMiddleware(): void
  {
    foreach($this->middleware() as $middleware) {
      $middleware->before($this->getMessage(), $this);
    }
  }

  /**
   * Handle all after middleware actions
   *
   * @return void
   */
  private function handleAfterMiddleware(ServerResponse $response): ServerResponse
  {
    foreach($this->middleware() as $middleware) {
      $response = $middleware->after($response, $this->getMessage(), $this);
    }

    return $response;
  }

  /**
   * Return user define middleware list
   *
   * @return array<MiddlewareInterface>
   */
  function middleware(): array
  {
    return [];
  }

  /**
   * Message text alias
   *
   * @return string
   */
  function getMessageText(): string
  {
    return $this->message->getText(true);
  }
}