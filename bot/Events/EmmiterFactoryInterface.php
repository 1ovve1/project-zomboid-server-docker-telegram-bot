<?php declare(strict_types=1);

namespace PZBot\Events;

interface EmmiterFactoryInterface
{
  function getEmmiter(): Emmiter;
}