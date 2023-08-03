<?php declare(strict_types=1);
namespace PZBot\Events\Handlers;
use DateTime;
use PZBot\Env;
use PZBot\Events\HandlerInterface;
use PZBot\Exceptions\Checked\PathWasNotFoundException;
use PZBot\Helpers\TelegramRequestHelper;
use PZBot\Service\ImageResolver;
use PZBot\Service\OpenAI\ChatGpt;


class ChatGptTsundereMessageHandler implements HandlerInterface
{
  protected Env $config;
  protected ImageResolver $imageReslover;
  /**
   * @var array<int, DateTime> $taskActivity - index tell about index task. Recorevy time: 24h
   */
  protected array $taskActivity = [];

  public function __construct(Env $config) {
    $this->config = $config;
    $this->imageReslover = new ImageResolver($config);
  }

  /**
   * Make bot speak by sheduler time message
   *
   * @param array{
   *      message: string, 
   *      time: DateTime
   * } ...$params - conatins array with string message and DateTime object
   * @return void
   */
  public function __invoke(mixed ...$params): void
  {
    foreach($params as $index => ['message' => $message, 'time' => $taskTime]) {

      if ($this->isTaskReady($index) && $this->isTime($taskTime))
      {
        $chatGpt = ChatGpt::fromEnv($this->config);
  
        $choice = $chatGpt->answerWithoutUserId("сгенерируй фразу, в которой ты в роли цундере девочки коротко говоришь \"{$message}\" в еë стиле. Без слов о том, что ты искуственный интеллект");
  
        $content = $choice->message->content;

        try {
          $imagePath = $this->imageReslover->getRandomPicturePath("/tohsaka");
  
          TelegramRequestHelper::sendImageToAllGroups($imagePath, $content);
        } catch (PathWasNotFoundException) {
          TelegramRequestHelper::sendMessageToAllGroups($content);
        } finally {
          $this->setTaskActivity($index);
        }
      }
    }
  }
  
  private function isTime(DateTime $compare): bool
  {
    $now = $this->now();
    $format = "Hi";

    return $now->format($format) === $compare->format(($format));
  }

  private function isTaskReady(int $taskIndex): bool
  {
    if (!isset($this->taskActivity[$taskIndex])) {
      return true;
    }

    $now = $this->now();
    $lastTaskActivity = $this->taskActivity[$taskIndex];

    if ($lastTaskActivity instanceof DateTime) {
      return $now->diff($lastTaskActivity)->h >= 23;
    } else {
      return true;
    }

  }
  private function setTaskActivity(int $taskIndex): void
  {
    $this->taskActivity[$taskIndex] = $this->now();
  }

  private function now(): DateTime
  {
    return new DateTime();
  }

}