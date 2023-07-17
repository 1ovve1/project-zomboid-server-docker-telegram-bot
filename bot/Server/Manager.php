<?php declare(strict_types=1);

namespace PZBot\Server;

use PZBot\Database\ServerStatus;
use PZBot\Exceptions\ServerManageException;

class Manager 
{
  const CMD_SHUTDOWN = "sudo docker-compose down";
  const CMD_UP = "sudo docker-compose up -d";
  const CMD_RESTART = "sudo docker-compose down && sudo docker-compose up";

  /**
   * Shutdown server
   *
   * @return void
   * @throws ServerManageException
   */
  public static function down() 
  {
    if (ServerStatus::isDown()) {
      throw new ServerManageException("Server already shutdown");
    }

    $status = shell_exec(self::CMD_SHUTDOWN);

    if ($status === null or $status === false) {
      throw new ServerManageException("Failed to shutdown server: please contact creator {$_ENV['CONFIG']['creator']['link']}");
    }

    ServerStatus::updateStatus(Status::DOWN);
  }

  /**
   * Up server
   *
   * @return void
   * @throws ServerManageException
   */
  public static function up(): void 
  {
    if (ServerStatus::isPending()) {
      throw new ServerManageException("Server already up. Please wait or contact creator {$_ENV['CONFIG']['creator']['link']}!");
    }

    $status = shell_exec(self::CMD_UP);

    if ($status === null or $status === false) {
      throw new ServerManageException("Failed to up server: please contact creator {$_ENV['CONFIG']['creator']['link']}");
    }

    ServerStatus::updateStatus(Status::DOWN);
  }

  /**
   * Restart server
   *
   * @return void
   * @throws ServerManageException
   */
  public static function restart(): void 
  {
    if (ServerStatus::isRestarted()) {
      throw new ServerManageException("Server already restarted");
    }

    $status = shell_exec(self::CMD_RESTART);

    if ($status === null or $status === false) {
      throw new ServerManageException("Failed to restart server: please contact creator {$_ENV['CONFIG']['creator']['link']}");
    }
  }
}