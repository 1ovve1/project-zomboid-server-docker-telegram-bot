<?php declare(strict_types=1);

namespace PZBot\Tests\Features\Events;
use PHPUnit\Framework\TestCase;
use PZBot\Events\Emmiter;
use PZBot\Events\EmitterFactoryInterface;
use PZBot\Events\EventsCollection;
use PZBot\Events\EventsEnum;
use PZBot\Tests\Mock\Events\MockEmmiterFactory;

class EmmiterTest extends TestCase
{
  const GLOBALS_KEY = "TEST";
  const EVENT_NAME_1 = "TEST_EVENT";
  const EVENT_NAME_2 = "TEST_EVENT";
  public Emmiter $emmiter;

  function setUp(): void
  {
    $this->emmiter = new Emmiter($this->generateEventsCollection(self::EVENT_NAME_1));

    unset($GLOBALS[self::GLOBALS_KEY]);
  }

  function testEmmit(): void
  {
    $this->assertArrayNotHasKey(self::GLOBALS_KEY, $GLOBALS);

    $this->emmiter->emmit(self::EVENT_NAME_1, self::EVENT_NAME_1);

    $this->assertArrayHasKey(self::GLOBALS_KEY, $GLOBALS);
    $this->assertEquals(self::EVENT_NAME_1, $GLOBALS[self::GLOBALS_KEY]);
  }

  function testUseCollection(): void
  {
    $newCollection = $this->generateEventsCollection(self::EVENT_NAME_2);

    $this->assertArrayNotHasKey(self::GLOBALS_KEY, $GLOBALS);

    $this->emmiter->emmit(self::EVENT_NAME_2, self::EVENT_NAME_2);

    $this->assertArrayHasKey(self::GLOBALS_KEY, $GLOBALS);
    $this->assertEquals(self::EVENT_NAME_2, $GLOBALS[self::GLOBALS_KEY]);


  }


  function generateEventsCollection(string $eventName): EventsCollection
  {
    $eventsCollection = new EventsCollection;
    
    $eventsCollection->addEventListener(
      $eventName, fn($param) => $GLOBALS[self::GLOBALS_KEY] = $param
    );

    return $eventsCollection;
  }
}