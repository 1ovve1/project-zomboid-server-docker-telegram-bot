<?php declare(strict_types=1);

namespace PZBot\Server\Commands;


interface CommandResolverInterface
{
  function resolve(CommandListEnum $commandEnum): string;
}