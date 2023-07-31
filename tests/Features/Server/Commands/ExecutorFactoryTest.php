<?php declare(strict_types=1);

namespace PZBot\Tests\Features\Server\Commands;
use PHPUnit\Framework\TestCase;
use PZBot\Server\Commands\ExecutorInterface;
use PZBot\Server\Commands\Factories\ExecutorFactory;
use PZBot\Server\Commands\Factories\ExecutorFactoryInterface;
use PZBot\Tests\Mock\Server\Commands\Bash\MockBashCommandResolver;

class ExecutorFactoryTest extends TestCase
{
  protected ExecutorFactoryInterface $executorFactory;


  function setUp(): void
  {
    $this->executorFactory = new ExecutorFactory(new MockBashCommandResolver);

    parent::setUp();
  }

  function testFactory(): void
  {
    $executor = $this->executorFactory->getExecutor();

    $this->assertInstanceOf(ExecutorInterface::class, $executor);
  }
}