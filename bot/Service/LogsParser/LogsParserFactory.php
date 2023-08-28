<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;
use PZBot\Exceptions\Checked\LogsFilePremissionDeniedException;
use PZBot\Exceptions\Checked\LogsFileWasNotFoundedException;
use PZBot\Service\LogsParser\Server\ServerStartParser;
use PZBot\Service\LogsParser\User\UserStatusParser;

class LogsParserFactory
{
  /**
   * @return ParserInterface
   * @throws LogsFileWasNotFoundedException
   * @throws LogsFilePremissionDeniedException
   */
  function getUserStatusParser(): ParserInterface
  {
    return UserStatusParser::create(
      ParserOptionsEnum::UNIQUE, ParserOptionsEnum::FROM_TOP
    );
  }

  /**
   * @return ParserInterface
   * @throws LogsFileWasNotFoundedException
   * @throws LogsFilePremissionDeniedException
   */
  function getServerStartParser(): ParserInterface
  {
    return ServerStartParser::create(
      ParserOptionsEnum::ONCE,
    );
  }
}