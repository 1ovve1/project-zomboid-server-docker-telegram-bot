<?php declare(strict_types=1);

namespace PZBot\Tests\Features\Server;
use PHPUnit\Framework\TestCase;
use PZBot\Server\Commands\Factories\ExecutorFactory;
use PZBot\Server\Factories\ManagerFactory;
use PZBot\Server\Factories\ManagerFactoryInterface;
use PZBot\Server\Manager;
use PZBot\Server\ManagerInterface;
use PZBot\Tests\Mock\Server\Commands\Bash\MockBashCommandResolver;

class ManagerFactoryTest extends TestCase
{
  protected ManagerFactoryInterface $managerFactory;

  function setUp(): void
  {
    $this->managerFactory = new ManagerFactory(
      new ExecutorFactory(
        new MockBashCommandResolver()
      )
    );

    parent::setUp();
  }

  function testBashManager(): void
  {
    $manager = $this->managerFactory->getManager();

    $this->assertInstanceOf(ManagerInterface::class, $manager);
    $this->assertInstanceOf(Manager::class, $manager);
  }
}