<?php declare(strict_types=1);

namespace PZBot\Tests\Features\Server\Commands\Bash;

use PHPUnit\Framework\TestCase;
use PZBot\Exceptions\Checked\ExecutorCommandException;
use PZBot\Server\Commands\CommandListEnum;
use PZBot\Server\Commands\CommandResultObject;
use PZBot\Server\Commands\Factories\ExecutorFactory;
use PZBot\Server\Commands\Factories\ExecutorFactoryInterface;
use PZBot\Tests\Mock\Server\Commands\Bash\MockBashCommandResolver;
use PZBot\Tests\Mock\Server\Commands\Bash\MockBashCommandResolverError;


class BashExecutorTest extends TestCase
{
  protected ExecutorFactoryInterface $executorFactory;
  protected ExecutorFactoryInterface $executorFactoryWithErrorReslover;

  function setUp(): void
  {
    $this->executorFactory = new ExecutorFactory(
      new MockBashCommandResolver
    );

    $this->executorFactoryWithErrorReslover = new ExecutorFactory(
      new MockBashCommandResolverError
    );

    parent::setUp();
  }

  function testCommandExecution(): void
  {
    $executor = $this->executorFactory->getExecutor();
    foreach(CommandListEnum::cases() as $command) {
      $commandResult = $executor->execute($command);
  
      $this->assertInstanceOf(CommandResultObject::class, $commandResult);
      $this->assertTrue($commandResult->isOK());
      $this->assertFalse($commandResult->isBad());
      $this->assertStringContainsString($command->exitCode(), $commandResult->getRawResult()); 
    }

  }

  function testCommandExecutionError(): void
  {
    $executor = $this->executorFactoryWithErrorReslover->getExecutor();
    
    $this->expectException(ExecutorCommandException::class);
    
    $executor->execute(...CommandListEnum::cases());
  }
}