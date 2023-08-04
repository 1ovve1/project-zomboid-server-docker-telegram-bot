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
  /** 
   * @var Env $config 
   */
  protected Env $config;
  /**
   * @var ImageResolver - service
   */
  protected ImageResolver $imageReslover;
  /**
   * @var array<int, DateTime> $throttleList - throttle list with date of the task execution
   */
  protected array $throttleList = [];


  /**
   * @param Env $config - config instance
   */
  public function __construct(Env $config) {
    $this->config = $config;
    $this->imageReslover = new ImageResolver($config);
  }

  /**
   * Make bot speak by sheduler time message
   *
   * @param array{
   *      message: string, 
   *      time: DateTime|false
   * } ...$params - conatins array with string message and DateTime object. If time keis is false task deactivated
   * @return void
   */
  public function __invoke(mixed ...$params): void
  {
    foreach($params as $index => ['message' => $message, 'time' => $taskTime]) {

      if (!$this->isTaskThrottle($index) && $this->isTime($taskTime))
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
          $this->setThrottleListPoint($index);
        }
      }
    }
  }

  /**
   * Comapare current time and task execution time
   * If taskTime equals false then isTime alwayes will be return false
   *
   * @param DateTime|boolean $compare
   * @return boolean
   */
  private function isTime(DateTime|false $taskTime): bool
  {
    if ($taskTime === false) {
      return false;
    }
    
    $now = $this->now();
    $format = "Hi";

    return $now->format($format) === $taskTime->format(($format));
  }

  /**
   * Check if task exists in throttle list 
   *
   * @param integer $taskIndex
   * @return boolean
   */
  private function isTaskThrottle(int $taskIndex): bool
  {
    if (!isset($this->throttleList[$taskIndex])) {
      return false;
    }

    if ($this->isTaskThrottleExpired($taskIndex)) {
      $this->clearThrottleListItem($taskIndex);
    }

    return true;
  }

  /**
   * alias for isTaskThrottle
   *
   * @param integer $taskIndex
   * @return boolean
   */
  private function isTaskNotThrottle(int $taskIndex): bool
  {
    return !$this->isTaskNotThrottle($taskIndex);
  }
  
  /**
   * Check if task throttle time is expired (by default it is 1 hour)
   *
   * @param int $taskIndex
   * @return boolean
   */
  private function isTaskThrottleExpired(int $taskIndex): bool
  {
    $lastthrottleListPoint = $this->throttleList[$taskIndex];

    return $this->now()->diff($lastthrottleListPoint)->h >= 1;
  }

  /**
   * Remove task from throttle list
   *
   * @param integer $taskIndex
   * @return void
   */
  private function clearThrottleListItem(int $taskIndex): void
  {
    unset($this->throttleList[$taskIndex]);
  }

  /**
   * Put task execution point in throttle list
   *
   * @param integer $taskIndex
   * @return void
   */
  private function setThrottleListPoint(int $taskIndex): void
  {
    $this->throttleList[$taskIndex] = $this->now();
  }

  /**
   * Return current time
   *
   * @return DateTime
   */
  private function now(): DateTime
  {
    return new DateTime();
  }

}