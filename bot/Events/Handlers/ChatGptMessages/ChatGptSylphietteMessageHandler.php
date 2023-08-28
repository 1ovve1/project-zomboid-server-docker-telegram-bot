<?php declare(strict_types=1);
namespace PZBot\Events\Handlers\ChatGptMessages;

class ChatGptSylphietteMessageHandler extends ChatGptMessage
{
  function getMessageFormat(): string
  {
    return "сгенерируй милую фразу, в которой ты в роли стеснительной девочки эльфийки коротко желаешь %s. Без слов о том, что ты искуственный интеллект. Не говоря о том, что ты стеснительная девочка эльфийка. Фараз должна быть адресована одному человеку. В предложении должна чувствоваться скромность и стеснение, а самое главное отсутствовать навязчивость";
  }

  function getImageFolder(): string
  {
    return '/sylphiette';
  }
}