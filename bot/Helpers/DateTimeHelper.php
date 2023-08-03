<?php declare(strict_types=1);

namespace PZBot\Helpers;
use DateInterval;
use DateTime;

class DateTimeHelper
{
  static function calculateTimeAfterDate(DateTime $time): string
  {
    $currentTime = new DateTime;
    $interval = $currentTime->diff($time);

    return self::printInterval($interval);
  }

  static function printInterval(DateInterval $dateInterval): string
  {
    if ($dateInterval->d > 0) {
      return sprintf(
        "%d days %d:%d:%d",
        $dateInterval->d, $dateInterval->h, $dateInterval->i, $dateInterval->s
      );
    } else {
      return sprintf(
        "%s:%s:%s",
        $dateInterval->h, $dateInterval->i, $dateInterval->s
      );
    }
  }
}