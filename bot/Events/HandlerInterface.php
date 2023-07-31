<?php declare(strict_types=1);
namespace PZBot\Events;

interface HandlerInterface
{
  public function __invoke(mixed ...$params): mixed;
}