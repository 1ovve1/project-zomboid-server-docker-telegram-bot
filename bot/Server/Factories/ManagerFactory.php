<?php declare(strict_types=1);

namespace PZBot\Server\Factories;

use PZBot\Server\Commands\Factories\ExecutorFactory;
use PZBot\Server\Commands\Factories\ExecutorFactoryInterface;
use PZBot\Server\Manager;
use PZBot\Server\ManagerInterface;

class ManagerFactory implements ManagerFactoryInterface
{
  protected ExecutorFactoryInterface $executorFactory;

  function __construct(ExecutorFactoryInterface $executorFactory) 
  {
    $this->executorFactory = $executorFactory;
  }

  function getManager(): ManagerInterface
  {
    $executor = $this->executorFactory->getExecutor();

    return new Manager($executor);
  }
}