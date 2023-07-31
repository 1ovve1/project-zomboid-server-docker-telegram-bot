<?php declare(strict_types=1);

namespace PZBot\Server\Factories;
use PZBot\Server\ManagerInterface;

interface ManagerFactoryInterface
{
  function getManager(): ManagerInterface;
}