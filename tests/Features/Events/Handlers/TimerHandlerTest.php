<?php declare(strict_types=1);

namespace PZBot\Tests\Features\Events\Handlers;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PZBot\Events\Handlers\TimerHandler;
use PZBot\Tests\Mock\Events\Handlers\MockEvent;

class TimerHandlerTest extends TestCase
{
  function setUp(): void
  {
    $GLOBALS[MockEvent::class] = null;
  }

  function tearDown(): void
  {
    $GLOBALS[MockEvent::class] = null;
  }

  function testAwakeable(): void
  {
    $event = new TimerHandler(
      new MockEvent(), (new DateTimeImmutable)->add(DateInterval::createFromDateString("10 milliseconds")), DateInterval::createFromDateString("10 millisecond")
    );
    $testData = ["test", "anotherTestData"];

    foreach ($testData as $case) {
      $event($case);

      $this->assertArrayHasKey(MockEvent::class, $GLOBALS);
      $this->assertNotEquals($case, $GLOBALS[MockEvent::class]);

      usleep(10000);
      $event($case);

      $this->assertArrayHasKey(MockEvent::class, $GLOBALS);
      $this->assertEquals($case, $GLOBALS[MockEvent::class]);
    }
    
  }

  function testOnce(): void
  {
    $event = (new TimerHandler(
      new MockEvent(), (new DateTimeImmutable)->add(DateInterval::createFromDateString("10 milliseconds")), DateInterval::createFromDateString("10 millisecond")
    ))->offAwakeable();
    $case = "test";

    $event($case);

    $this->assertArrayHasKey(MockEvent::class, $GLOBALS);
    $this->assertNotEquals($case, $GLOBALS[MockEvent::class]);

    usleep(10000);
    $event($case);

    $this->assertArrayHasKey(MockEvent::class, $GLOBALS);
    $this->assertEquals($case, $GLOBALS[MockEvent::class]);

    // change case data
    $case = "another data";
    
    usleep(10000);
    $event($case);

    $this->assertArrayHasKey(MockEvent::class, $GLOBALS);
    $this->assertNotEquals($case, $GLOBALS[MockEvent::class]);

  }
}