<?php declare(strict_types=1);
namespace PZBot\Events\Handlers;

use PZBot\Events\HandlerInterface;
use PZBot\Exceptions\Checked\PathWasNotFoundException;
use PZBot\Helpers\TelegramRequestHelper;
use PZBot\Service\ImageResolver;
use PZBot\Service\OpenAI\ChatGpt;


class ChatGptTsundereMessageHandler implements HandlerInterface
{
  /**
   * @var ImageResolver - service
   */
  protected ImageResolver $imageResolver;
  /**
   * @var ChatGpt - service
   */
  protected ChatGpt $chatGpt;

  public function __construct(ImageResolver $imageResolver, ChatGpt $chatGpt) {
    $this->imageResolver = $imageResolver;
    $this->chatGpt = $chatGpt;
  }

  /**
   * Make bot speak by scheduler time message
   *
   * @param string ...$params - contains array with string message and DateTime object. If time keis is false task deactivated
   * @return void
   */
  public function __invoke(mixed ...$params): void
  {
    foreach ($params as $message) {
      $choice = $this->chatGpt->answerWithoutUserId("сгенерируй фразу, в которой ты в роли цундере девочки коротко говоришь \"{$message}\" в еë стиле. Без слов о том, что ты искуственный интеллект");

      $content = $choice->message->content;

      try {
        $imagePath = $this->imageResolver->getRandomPicturePath("/tohsaka");

        TelegramRequestHelper::sendImageToAllGroups($imagePath, $content);
      } catch (PathWasNotFoundException) {
        TelegramRequestHelper::sendMessageToAllGroups($content);
      }
    }
  }
}