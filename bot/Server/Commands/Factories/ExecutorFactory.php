<?php declare(strict_types=1);

namespace PZBot\Server\Commands\Factories;
use PZBot\Server\Commands\CommandResolverInterface;
use PZBot\Server\Commands\Executor;
use PZBot\Server\Commands\ExecutorInterface;


class ExecutorFactory implements ExecutorFactoryInterface
{
  protected CommandResolverInterface $resolver;

  public function __construct(CommandResolverInterface $resolver) 
  {
    $this->resolver = $resolver;
  }

  /**
   * @inheritDoc
   */
  function getExecutor(): ExecutorInterface
  {
    return new Executor($this->resolver);
  }

  /**
   * @inheritDoc
   */
  function getExecutorUnsafe(): ExecutorInterface
  {
    return new Executor($this->resolver, false);
  }
}