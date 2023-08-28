<?php declare(strict_types=1);

namespace PZBot\Tests\Unit;
use PHPUnit\Framework\TestCase;
use PZBot\Config\Env;
use PZBot\Exceptions\Unchecked\EnvParameterNotFoundException;

class EnvTest extends TestCase
{
  const ENV_VALUES = [
    "TEST" => 123456,
    "TEST_COMPLEX" => [
      "BRANCH1" => 123,
      "BRANCH2" => [
        "BRANCH3" => 456,
      ],
    ]
  ];
  public Env $config;

  function setUp(): void
  {
    $_ENV = self::ENV_VALUES;
    $this->config = new Env();

    parent::setUp();
  }

  function testSimpleParam(): void
  {
    $param = $this->config->get("TEST");

    $this->assertEquals(self::ENV_VALUES["TEST"], $param);
  }

  function testComplexParam(): void
  {
    $param = $this->config->get("TEST_COMPLEX.BRANCH1");

    $this->assertEquals(self::ENV_VALUES["TEST_COMPLEX"]["BRANCH1"], $param);
  }

  function testException(): void
  {
    $this->expectException(EnvParameterNotFoundException::class);

    $this->config->get("TEST_COMPLEX.BRANCH3");
  }
}