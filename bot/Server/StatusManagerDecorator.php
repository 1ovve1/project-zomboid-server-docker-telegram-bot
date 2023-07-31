<?php declare(strict_types=1);

namespace PZBot\Server;
use PZBot\Database\ServerStatus;
use PZBot\Exceptions\Checked\ServerManageException;
use PZBot\Exceptions\Checked\UnknownServerManagerError;

class StatusManagerDecorator implements ManagerInterface
{
  private Manager $manager;

  public function __construct(Manager $manager) {
    $this->manager = $manager;
  }

  /**
   * @inheritDoc
   */
  public function down(): void
  {
    if (ServerStatus::isDown()) {
      throw new ServerManageException("Server already shutdown");
    }

    $lastStatus = ServerStatus::getLastStatus();

    ServerStatus::updateStatus(StatusEnum::DOWN);

    try {
      $this->getManager()->down();
    } catch (UnknownServerManagerError $e) {
      ServerStatus::updateStatus($lastStatus);

      throw new ServerManageException("Failed to down server");
    }
  }

  /**
   * @inheritDoc
   */
  public function up(): void
  {
    if (ServerStatus::isPending() || ServerStatus::isActive()) {
      throw new ServerManageException("Server already up. Please wait!");
    }

    $lastStatus = ServerStatus::getLastStatus();

    ServerStatus::updateStatus(StatusEnum::PENDING);

    try {
      $this->getManager()->up();
    } catch (UnknownServerManagerError $e) {
      ServerStatus::updateStatus($lastStatus);

      throw new ServerManageException("Failed to up server");
    }
  }

  /**
   * @inheritDoc
   */
  public function restart(): void
  {
    if (ServerStatus::isRestarted()) {
      throw new ServerManageException("Server already restarted");
    }

    $lastStatus = ServerStatus::getLastStatus();

    ServerStatus::updateStatus(StatusEnum::RESTART);

    try {
      $this->getManager()->restart();
    } catch (UnknownServerManagerError $e) {
      ServerStatus::updateStatus($lastStatus);

      throw new ServerManageException("Failed to restart server");
    }
  }

  protected function getManager(): Manager
  {
    return $this->manager;
  }
}