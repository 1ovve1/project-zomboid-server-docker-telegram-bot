<?php declare(strict_types=1);

namespace PZBot\Events;
use PZBot\Exceptions\Checked\EventsNotFoundException;

class Emmiter
{
  protected EventsCollection $collection;

  /**
   * @param EventsCollection $eventsCollection - if null create empty collection
   */
  function __construct(EventsCollection $eventsCollection) 
  {
    $this->collection = $eventsCollection;
  }

  /**
   * Add collection events to emmiter
   *
   * @param EventsCollection $eventsCollection
   * @return void
   */
  function useCollection(EventsCollection $eventsCollection): void
  {
    $this->collection->merge($eventsCollection);
  }

  /**
   * Emmit events by name and given params
   *
   * @param EventsEnum|string $eventName
   * @param mixed ...$params
   * @return void
   */
  function emmit(EventsEnum|string $eventName, mixed ...$params): void
  {
    try {
      $events = $this->collection->getEventsByEnum($eventName);

      foreach($events as $clbk) 
      {
        $clbk(...$params);
      }
    } catch(EventsNotFoundException) {
      //...
    }
  }
}