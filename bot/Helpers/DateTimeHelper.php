<?php declare(strict_types=1);

namespace PZBot\Helpers;
use DateTime;

class DateTimeHelper
{
  static function calculateTimeAfterDate(DateTime $time): string
  {
    $currentTime = new DateTime;
    $interval = $currentTime->diff($time);

    if ($interval->d > 0) {
      return sprintf(
        "%d days %d:%d:%d",
        $interval->d, $interval->h, $interval->i, $interval->s
      );
    } else {
      return sprintf(
        "%s:%s:%s",
        $interval->h, $interval->i, $interval->s
      );
    }
  }
}