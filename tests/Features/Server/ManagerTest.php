<?php declare(strict_types=1);

namespace PZBot\Tests\Features\Server;

use PHPUnit\Framework\TestCase;
use PZBot\Exceptions\Checked\UnknownServerManagerError;
use PZBot\Server\Commands\Factories\ExecutorFactory;
use PZBot\Server\Factories\ManagerFactory;
use PZBot\Server\Factories\ManagerFactoryInterface;
use PZBot\Tests\Mock\Server\Commands\Bash\MockBashCommandResolver;
use PZBot\Tests\Mock\Server\Commands\Bash\MockBashCommandResolverError;

class ManagerTest extends TestCase
{
  protected ManagerFactoryInterface $managerFactory;
  protected ManagerFactoryInterface $managerFactoryWithError;

  function setUp(): void
  {
    $this->managerFactory = new ManagerFactory(
      new ExecutorFactory(
        new MockBashCommandResolver
      )
    );

    $this->managerFactoryWithError = new ManagerFactory(
      new ExecutorFactory(
        new MockBashCommandResolverError
      )
    );

    parent::setUp();
  }

  function testBashManagerUp(): void
  {
    $bashManager = $this->managerFactory->getManager();

    $bashManager->up();

    $bashManagerWithError = $this->managerFactoryWithError->getManager();

    $this->expectException(UnknownServerManagerError::class);

    $bashManagerWithError->up();
  }

  function testBashManagerDown(): void
  {
    $bashManager = $this->managerFactory->getManager();

    $bashManager->down();

    $bashManagerWithError = $this->managerFactoryWithError->getManager();

    $this->expectException(UnknownServerManagerError::class);

    $bashManagerWithError->down();
  }

  function testBashManagerRestart(): void
  {
    $bashManager = $this->managerFactory->getManager();

    $bashManager->restart();

    $bashManagerWithError = $this->managerFactoryWithError->getManager();

    $this->expectException(UnknownServerManagerError::class);

    $bashManagerWithError->restart();
  }

}