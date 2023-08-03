<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser\DTO;
use DateInterval;
use DateTime;
use PZBot\Service\LogsParser\UserStatusEnum;

class UserActivityObject implements UniqueDTOInterface
{
  function __construct(
    readonly int $id,
    readonly string $userName,
    readonly UserStatusEnum $status,
    readonly DateTime $activityTime,
  ) 
  {}

  /**
   * Create user activity DTO object from array params
   *
   * @param array{
   *    time: string,
   *    name: string,
   *    id: string,
   *    action: string
   * } $params
   * @return self
   */
  static function fromStringArray(array $params): self
  {
    return new self(
      (int)$params["id"],
      $params["name"],
      UserStatusEnum::find($params["action"]),
      DateTime::createFromFormat('d-m-y H:i:s.v', $params["time"])->add(new DateInterval("PT3H")), // TODO: pz use default timezone need to solve it in better way
    );
  }

  /**
   * @inheritDoc
   */
  function getId(): int
  {
    return $this->id;
  }
  
  function toString(): string
  {
    return sprintf(
      "%s %s \t(%s)",
      $this->status->emoji(),
      $this->userName,
      $this->status->resolveTime($this->activityTime),
    );
  }
}