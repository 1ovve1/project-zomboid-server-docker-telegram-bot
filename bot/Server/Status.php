<?php declare(strict_types=1);

namespace PZBot\Server;


enum Status: string
{
  case ACTIVE = "ACTIVE";
  case RESTART = "RESTART";
  case DOWN = "DOWN";
  case PENDIGN = "PENDING";
  case UNDEFINED = "UNDEFINED";
}