<?php declare(strict_types=1);

namespace PZBot\Server;

use Longman\TelegramBot\TelegramLog;
use PZBot\Server\Commands\CommandListEnum;
use PZBot\Exceptions\Checked\ExecutorCommandException;
use PZBot\Exceptions\Checked\UnknownServerManagerError;
use PZBot\Server\Commands\ExecutorInterface;

class Manager implements ManagerInterface
{
  protected ?ExecutorInterface $executor;

  function __construct(?ExecutorInterface $executor = null)
  {
    $this->executor = $executor;
  }
  
  /**
   * @inheritDoc
   */
  public function down(): void
  {
    try {
      $this->executor->execute(CommandListEnum::SERVER_DOWN);
    } catch (ExecutorCommandException $e) {
      throw new UnknownServerManagerError("Failed to shutdown server", $e);
    }
  }

  /**
   * @inheritDoc
   */
  public function up(): void 
  {
    $this->deleteLogs();

    try {
      $this->executor->execute(CommandListEnum::SERVER_UP);
    } catch (ExecutorCommandException $e) {
      throw new UnknownServerManagerError("Failed to up server, $e");
    }
  }

  /**
   * @inheritDoc
   */
  public function restart(): void 
  {
    try {
      $this->down();
    } catch(ExecutorCommandException $e) {
      TelegramLog::warning("Failed to down server", [$e]);
    } finally {
      $this->up();
    }
  }

  /**
   * Wrapper for delete logs command
   * 
   * @return void
   */
  public function deleteLogs(): void
  {
    try {
      $this->executor->execute(CommandListEnum::GAME_LOGS_DELETE);
    } catch(ExecutorCommandException $e) {
      TelegramLog::warning("Delete logs operation failed", [$e]);
    }
  }
}