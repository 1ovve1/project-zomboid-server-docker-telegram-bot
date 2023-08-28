<?php declare(strict_types=1);

namespace PZBot\Tests\Mock\Events\Handlers;
use PZBot\Events\HandlerInterface;

class MockEvent implements HandlerInterface
{
  function __invoke(mixed ...$params): void
  {
    $GLOBALS[self::class] = end($params);
  }
}