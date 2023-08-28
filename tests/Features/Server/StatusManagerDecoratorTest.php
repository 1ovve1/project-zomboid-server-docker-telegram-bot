<?php declare(strict_types=1);

namespace PZBot\Tests\Features\Server;

use PHPUnit\Framework\TestCase;
use PZBot\Database\ServerStatus;
use PZBot\Exceptions\Checked\CheckedException;
use PZBot\Exceptions\Checked\ServerManageException;
use PZBot\Exceptions\Checked\UnknownServerManagerError;
use PZBot\Server\Commands\Factories\ExecutorFactory;
use PZBot\Server\Factories\ManagerFactory;
use PZBot\Server\Factories\ManagerFactoryInterface;
use PZBot\Server\Factories\StatusManagerFactory;
use PZBot\Server\ServerStatusEnum;
use PZBot\Tests\Mock\Server\Commands\Bash\MockBashCommandResolver;
use PZBot\Tests\Mock\Server\Commands\Bash\MockBashCommandResolverError;

class StatusManagerDecoratorTest extends TestCase
{
  const DEAFULT_SERVER_STATUS = ServerStatusEnum::UNDEFINED;

  protected ManagerFactoryInterface $managerFactory;
  protected ManagerFactoryInterface $managerFactoryWithError;

  function setUp(): void
  {
    ServerStatus::updateStatus(self::DEAFULT_SERVER_STATUS);

    $this->managerFactory = new StatusManagerFactory(
      new ExecutorFactory(
        new MockBashCommandResolver
      )
    );

    $this->managerFactoryWithError = new StatusManagerFactory(
      new ExecutorFactory(
        new MockBashCommandResolverError
      )
    );

    parent::setUp();
  }

  function testStatusManagerDecoratorUp(): void
  {
    $bashManager = $this->managerFactory->getManager();

    $bashManager->up();

    $this->assertTrue(ServerStatus::isPending());
  }

  function testStatusManagerDecoratorUpError(): void
  {
    $bashManagerWithError = $this->managerFactoryWithError->getManager();

    $this->expectException(ServerManageException::class);

    $bashManagerWithError->up();
  }

  function testStatusManagerDecoratorUpErrorFallback(): void
  {
    $bashManagerWithError = $this->managerFactoryWithError->getManager();

    try {
      $bashManagerWithError->up();
    } catch(ServerManageException $e) {}

    $this->assertEquals(self::DEAFULT_SERVER_STATUS, ServerStatus::getLastStatus());
  }

  function testStatusManagerDecoratorDown(): void
  {
    $bashManager = $this->managerFactory->getManager();

    $bashManager->down();

    $this->assertTrue(ServerStatus::isDown());
  }

  function testStatusManagerDecoratorDownError(): void
  {
    $bashManagerWithError = $this->managerFactoryWithError->getManager();

    $this->expectException(ServerManageException::class);

    $bashManagerWithError->down();
  }

  function testStatusManagerDecoratorDownErrorFallback(): void
  {
    $bashManagerWithError = $this->managerFactoryWithError->getManager();

    try {
      $bashManagerWithError->down();
    } catch(ServerManageException $e) {}

    $this->assertEquals(self::DEAFULT_SERVER_STATUS, ServerStatus::getLastStatus());
  }

  function testStatusManagerDecoratorRestart(): void
  {
    $bashManager = $this->managerFactory->getManager();

    $bashManager->restart();

    $this->assertTrue(ServerStatus::isRestarted());
  }

  function testStatusManagerDecoratorRestartError(): void
  {
    $bashManagerWithError = $this->managerFactoryWithError->getManager();

    $this->expectException(ServerManageException::class);

    $bashManagerWithError->restart();
  }

  function testStatusManagerDecoratorRestartErrorFallback(): void
  {
    $bashManagerWithError = $this->managerFactoryWithError->getManager();

    try {
      $bashManagerWithError->restart();
    } catch(ServerManageException $e) {}

    $this->assertEquals(self::DEAFULT_SERVER_STATUS, ServerStatus::getLastStatus());
  }
}