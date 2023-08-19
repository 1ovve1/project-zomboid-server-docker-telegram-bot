<?php declare(strict_types=1);

namespace PZBot\Events\Handlers;

use Longman\TelegramBot\TelegramLog;
use PZBot\Database\ServerStatus;
use PZBot\Events\HandlerInterface;
use PZBot\Exceptions\Checked\ExecutorCommandException;
use PZBot\Exceptions\Checked\LogsFilePremissionDeniedException;
use PZBot\Exceptions\Checked\LogsFileWasNotFoundedException;
use PZBot\Server\Commands\CommandListEnum;
use PZBot\Server\Commands\Factories\ExecutorFactoryInterface;
use PZBot\Server\ServerStatusEnum;
use PZBot\Service\LogsParser\LogsParserFactory;
use PZBot\Service\LogsParser\ParserInterface;

class ServerStatusHandler implements HandlerInterface
{
  /**
   * @var LogsParserFactory
   */
  private LogsParserFactory $logsParserFactory;
  /**
   * @var ExecutorFactoryInterface
   */
  private ExecutorFactoryInterface $executorFactory;

  /**
   * @param LogsParserFactory $logsParserFactory
   * @param ExecutorFactoryInterface $executorFactory
   */
  public function __construct(LogsParserFactory $logsParserFactory, ExecutorFactoryInterface $executorFactory)
  {
    $this->logsParserFactory = $logsParserFactory;
    $this->executorFactory = $executorFactory;
  }

  /**
   * Dynamically change status based on log information
   *
   * @param mixed ...$params
   * @return void
   */
  public function __invoke(mixed ...$params): void
  {
    try {
      $result = $this->executorFactory
          ->getExecutor()
          ->execute(CommandListEnum::SERVER_STATUS);

      if ($result->isOK()) {
        $logsParser = $this->logsParserFactory->getServerStartParser();
        $parseResult = $logsParser->parse();

        if (count($parseResult) > 0) {
          ServerStatus::updateStatus(ServerStatusEnum::ACTIVE);
        } else {
          if (!ServerStatus::isRestarted()) {
            ServerStatus::updateStatus(ServerStatusEnum::PENDING);
          }
        }
      } else {
        if (!ServerStatus::isRestarted()) {
          ServerStatus::updateStatus(ServerStatusEnum::DOWN);
        }
      }
    } catch (ExecutorCommandException|LogsFilePremissionDeniedException|LogsFileWasNotFoundedException $e) {
      ServerStatus::updateStatus(ServerStatusEnum::DOWN);
    } catch (\Throwable) {
      ServerStatus::updateStatus(ServerStatusEnum::UNDEFINED);
    }

  }
}