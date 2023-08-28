<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser\DTO;

use PZBot\Server\ServerStatusEnum;

class ServerStatusObject implements UniqueDTOInterface
{
  function __construct(
    readonly ServerStatusEnum $serverStatusEnum,
  )
  {}

  function getId(): int
  {
    return 0;
  }

  
}