<?php declare(strict_types=1);

namespace PZBot\Server;

use PZBot\Database\ServerStatus;
use PZBot\Exceptions\Checked\ServerManageException;
use PZBot\Exceptions\Checked\UnknownServerManagerError;

class Manager 
{
  const CMD_SHUTDOWN = "sudo docker-compose down && echo 1";
  const CMD_UP = "rm ./data/Logs/*.txt && sudo docker-compose up -d && echo 1";
  const CMD_RESTART = "sudo docker-compose down && rm ./data/Logs/*.txt && sudo docker-compose up -d && echo 1";

  /**
   * Shutdown server
   *
   * @return void
   * @throws ServerManageException
   * @throws UnknownServerManagerError
   */
  public static function down() 
  {
    if (ServerStatus::isDown()) {
      throw new ServerManageException("Server already shutdown");
    }

    $status = shell_exec(self::CMD_SHUTDOWN);

    if ($status === null or $status === false) {
      throw new UnknownServerManagerError("Failed to shutdown server");
    }

    ServerStatus::updateStatus(Status::DOWN);
  }

  /**
   * Up server
   *
   * @return void
   * @throws ServerManageException
   * @throws UnknownServerManagerError
   */
  public static function up(): void 
  {
    if (ServerStatus::isPending() || ServerStatus::isActive()) {
      throw new ServerManageException("Server already up. Please wait!");
    }

    $status = shell_exec(self::CMD_UP);

    if ($status === null or $status === false) {
      throw new UnknownServerManagerError("Failed to up server");
    }

    ServerStatus::updateStatus(Status::PENDING);
  }

  /**
   * Restart server
   *
   * @return void
   * @throws ServerManageException
   * @throws UnknownServerManagerError
   */
  public static function restart(): void 
  {
    if (ServerStatus::isRestarted()) {
      throw new ServerManageException("Server already restarted");
    }

    $status = shell_exec(self::CMD_RESTART);

    if ($status === null or $status === false) {
      throw new UnknownServerManagerError("Failed to restart server");
    }

    ServerStatus::updateStatus(Status::RESTART);
  }
}