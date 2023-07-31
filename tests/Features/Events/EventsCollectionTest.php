<?php declare(strict_types=1);

namespace PZBot\Tests\Features\Events;

use PHPUnit\Framework\TestCase;
use PZBot\Events\EventsCollection;
use PZBot\Events\EventsEnum;

class EventsCollectionTest extends TestCase
{
  protected EventsCollection $eventsCollection;

  function setUp(): void
  {
    $this->eventsCollection = new EventsCollection;

    parent::setUp();
  }

  function testAddEvent(): void
  {    
    foreach(EventsEnum::cases() as $eventEnum) {
      $callback = static fn($param) => "{$eventEnum->value}: {$param}";

      $this->eventsCollection->addEventListener($eventEnum, $callback);
    }

    foreach (EventsEnum::cases() as $index => $eventEnum) {
      $handlers = $this->eventsCollection->getEventsByEnum($eventEnum);

      foreach ($handlers as $handler) {
        $this->assertEquals("{$eventEnum->value}: {$index}", $handler($index));
      }
    }
  }

  function testMergeEvent(): void
  {
    $callback1 = static fn() => 123;
    $callback2 = static fn() => 456;
    $callback3 = static fn() => 789;

    
    $this->eventsCollection->addEventListener(EventsEnum::BEFORE_HANDLE_UPDATES, $callback1);
    
    $newEventsCollection = new EventsCollection;
    $newEventsCollection->addEventListener(EventsEnum::BEFORE_HANDLE_UPDATES, $callback2);
    $newEventsCollection->addEventListener(EventsEnum::BEFORE_HANDLE_UPDATES, $callback3);

    $this->eventsCollection->merge($newEventsCollection);

    $handlers = $this->eventsCollection->getEventsByEnum(EventsEnum::BEFORE_HANDLE_UPDATES);

    $this->assertCount(3, $handlers);
    $this->assertEquals($callback1(), $handlers[0]());
    $this->assertEquals($callback2(), $handlers[1]());
    $this->assertEquals($callback3(), $handlers[2]());
  }
}
