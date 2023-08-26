<?php

namespace PZBot\Events\Handlers\ChatGptMessages;

use PZBot\Events\HandlerInterface;
use PZBot\Exceptions\Checked\PathWasNotFoundException;
use PZBot\Helpers\TelegramRequestHelper;
use PZBot\Service\ImageResolver;
use PZBot\Service\OpenAI\ChatGpt;

abstract class ChatGptMessage implements HandlerInterface
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
      $choice = $this->chatGpt->answerWithoutUserId(
          sprintf(
              $this->getMessageFormat(),
              $message
          )
      );

      $content = $choice->message->content;

      try {
        $imagePath = $this->imageResolver->getRandomPicturePath($this->getImageFolder());

        TelegramRequestHelper::sendImageToAllGroups($imagePath, $content);
      } catch (PathWasNotFoundException) {
        TelegramRequestHelper::sendMessageToAllGroups($content);
      }
    }
  }

  function getMessageFormat(): string
  {
    return '%';
  }
  function getImageFolder(): string
  {
    return '';
  }
}