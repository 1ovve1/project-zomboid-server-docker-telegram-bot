<?php declare(strict_types=1);

namespace PZBot\Events\Handlers;

use DateInterval;
use DateTimeImmutable;
use PZBot\Events\HandlerInterface;

use function call_user_func;

class TimerHandler implements HandlerInterface
{
  protected bool $awakeable = true;
  protected HandlerInterface $callback;
  protected DateTimeImmutable $awakeTime;
  protected DateInterval $delay;

  function __construct(HandlerInterface $callback, ?DateTimeImmutable $awakeTime = null, ?DateInterval $delay = null)
  {
    $awakeTime ??= new DateTimeImmutable;
    $delay ??= DateInterval::createFromDateString('1 microsecond');

    if ((new DateTimeImmutable()) < $awakeTime) {
      $awakeTime = $awakeTime->sub($delay);
    }

    $this->callback = $callback;
    $this->awakeTime = $awakeTime;
    $this->delay = $delay;
  }

  static function fromString(HandlerInterface $callback, string $awakeTime, string $delay): self
  {
    return new self(
      $callback,
      DateTimeImmutable::createFromFormat("H:i", $awakeTime),
      DateInterval::createFromDateString($delay)
    );
  }

  function __invoke(mixed ...$params): void
  {
    if ($this->isAwakeable()) {
      $this->callWithAwake(...$params);
    } else {
      $this->callOnce(...$params);
    }
  }

  private function callOnce(mixed ...$params): void
  {
    static $once = true;

    if ($once && $this->isTime()) {
      $this->callCallback(...$params);
      $once = false;
    }
  }

  private function callWithAwake(mixed ...$params): void
  {
    if ($this->isTime()) {
      $this->callCallback(...$params);
    }
  }

  private function callCallback(mixed ...$params): void
  {
    call_user_func($this->callback, ...$params);
  }

  private function isTime(): bool
  {
    $now = new DateTimeImmutable();
    
    $awakeTime = $this->awakeTime->add($this->delay);

    $compare = $now >= $awakeTime;

    if ($compare) {
      $this->awakeTime = $awakeTime;
    }

    return $compare;
  }

  function onAwakeable(): self
  {
    $this->awakeable = true;
    
    return $this;
  }

  function offAwakeable(): self
  {
    $this->awakeable = false;

    return $this;
  }

  function isAwakeable(): bool
  {
    return $this->awakeable;
  }
}