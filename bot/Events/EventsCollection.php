<?php declare(strict_types=1);

namespace PZBot\Events;
use PZBot\Exceptions\Checked\EventsNotFoundException;


class EventsCollection
{
  /**
   * @var array<string, array<callable>> $events
   */
  protected array $events = [];


  /**
   * @param EventsEnum|string $eventName
   * @param callable $callback
   * @return void
   */
  function addEventListener(EventsEnum|string $eventName, callable $callback): void
  {
    if ($eventName instanceof EventsEnum) {
      $eventName = $eventName->value;
    }

    $this->events[$eventName][] = $callback;
  }

  /**
   * @param EventsEnum $eventName
   * @return array<callable>
   * @throws EventsNotFoundException
   */
  function getEventsByEnum(EventsEnum|string $eventName): array
  {
    if ($eventName instanceof EventsEnum) {
      $eventName = $eventName->value;
    }

    return $this->events[$eventName] ?? throw new EventsNotFoundException($eventName);
  }

  /**
   * Merge events with given collection
   *
   * @param EventsCollection $eventsCollection
   * @return void
   */
  function merge(EventsCollection $eventsCollection): void
  {
    foreach($eventsCollection->getEvents() as $eventName => $clbks) {
      $this->events[$eventName] = match(isset($this->events[$eventName])) {
        true => array_merge($this->events[$eventName], $clbks),
        false => $clbks,
      };
    }
  }

  /**
   * @return array<string, array<callable>>
   */
  function getEvents(): array
  {
    return $this->events;
  }
}
