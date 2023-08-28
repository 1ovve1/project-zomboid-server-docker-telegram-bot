<?php declare(strict_types=1);

namespace PZBot\Events;

interface EmitterFactoryInterface
{
  function getEmitter(): Emmiter;
}