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
      $answer = $this->chatGpt->answerWithoutUserIdAndMemory(
          sprintf(
              $this->getMessageFormat(),
              $message
          )
      );

      try {
        $imagePath = $this->imageResolver->getRandomPicturePathUnique($this->getImageFolder());

        TelegramRequestHelper::sendImageToAllGroups($imagePath, $answer);
      } catch (PathWasNotFoundException) {
        TelegramRequestHelper::sendMessageToAllGroups($answer);
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