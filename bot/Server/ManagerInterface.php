<?php declare(strict_types=1);

namespace PZBot\Server;

use PZBot\Exceptions\Checked\ServerManageException;
use PZBot\Exceptions\Checked\UnknownServerManagerError;

interface ManagerInterface
{
  /**
   * Shutdown server
   *
   * @return void
   * @throws UnknownServerManagerError|ServerManageException
   */
  public function down(): void;

  /**
   * Up server
   *
   * @return void
   * @throws UnknownServerManagerError|ServerManageException
   */
  public function up(): void;

  /**
   * Restart server
   *
   * @return void
   * @throws UnknownServerManagerError|ServerManageException
   */
  public function restart(): void;
}