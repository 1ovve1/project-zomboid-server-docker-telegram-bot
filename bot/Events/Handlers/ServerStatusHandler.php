<?php declare(strict_types=1);

namespace PZBot\Events\Handlers;

use Longman\TelegramBot\TelegramLog;
use PZBot\Database\ServerStatus;
use PZBot\Events\HandlerInterface;
use PZBot\Exceptions\Checked\LogsFilePremissionDeniedException;
use PZBot\Exceptions\Checked\LogsFileWasNotFoundedException;
use PZBot\Server\ServerStatusEnum;
use PZBot\Service\LogsParser\LogsParserFactory;
use PZBot\Service\LogsParser\ParserInterface;

class ServerStatusHandler implements HandlerInterface
{
  /**
   * @var ParserInterface
   */
  private LogsParserFactory $logsParserFactory;

  /**
   * @param LogsParserFactory $executorFactory
   */
  public function __construct(LogsParserFactory $logsParserFactory) 
  {
    $this->logsParserFactory = $logsParserFactory;
  }

  /**
   * Dinamicly change status based on log information
   * TODO: replace Executor usage on special LogsParser service
   *
   * @param mixed ...$params
   * @return void
   */
  public function __invoke(mixed ...$params): void
  {
    try {
      $logsParser = $this->logsParserFactory->getServerStartParser();
      $parseResult = $logsParser->parse();

      if (count($parseResult) > 0) {
        $this->cache = true;
        ServerStatus::updateStatus(ServerStatusEnum::ACTIVE);
      } else {
        if (!ServerStatus::isRestarted()) {
          ServerStatus::updateStatus(ServerStatusEnum::PENDING);
        }
      }
    } catch (LogsFileWasNotFoundedException) {
      ServerStatus::updateStatus(ServerStatusEnum::DOWN);
    } catch (LogsFilePremissionDeniedException) {
      TelegramLog::error("CANNOT UPDATE SERVER STATUS: please run bot with SUDO premissions");
      ServerStatus::updateStatus(ServerStatusEnum::DOWN);
    }
  }
}