<?php declare(strict_types=1);
namespace PZBot\Events\Handlers\ChatGptMessages;


class ChatGptTsundereMessageHandler extends ChatGptMessage
{
  function getMessageFormat(): string
  {
    return "сгенерируй фразу, в которой ты в роли цундере девочки коротко говоришь \"%s\" в еë стиле. Без слов о том, что ты искуственный интеллект";
  }

  function getImageFolder(): string
  {
    return '/tohsaka';
  }
}