<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;
use PZBot\Service\LogsParser\Server\ServerStartParser;
use PZBot\Service\LogsParser\User\UserStatusParser;

class LogsParserFactory
{
  /**
   * @return ParserInterface
   * @throws \PZBot\Exceptions\Checked\LogsFileWasNotFoundedException
   * @throws \PZBot\Exceptions\Checked\LogsFilePremissionDeniedException
   */
  function getUserStatusParser(): ParserInterface
  {
    return UserStatusParser::create(
      ParserOptionsEnum::UNIQUE, ParserOptionsEnum::FROM_TOP
    )->setLimit(100);
  }

  /**
   * @return ParserInterface
   * @throws \PZBot\Exceptions\Checked\LogsFileWasNotFoundedException
   * @throws \PZBot\Exceptions\Checked\LogsFilePremissionDeniedException
   */
  function getServerStartParser(): ParserInterface
  {
    return ServerStartParser::create(
      ParserOptionsEnum::ONCE,
    );
  }
}