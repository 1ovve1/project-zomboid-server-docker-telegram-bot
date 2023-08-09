<?php declare(strict_types=1);

namespace PZBot\Events\Handlers;

use PZBot\Events\HandlerInterface;
use RuntimeException;

class HandlersCollection implements HandlerInterface
{
  protected array $collection = [];

  private function __construct(array $collection) {
    $this->collection = $collection;
  }

  /**
   * Create collection by given array with:
   * 
   * [
   *  'handler_id_1' => HandlerInterface,
   *  'handler_id_2' => HandlerInterface,
   *  ...
   * ]
   * 
   * @param array<string, HandlerInterface> $collection
   * @return self
   */
  static function fromArray(array $collection): self
  {
    foreach ($collection as $key => $value) {
      if (!($value instanceof HandlerInterface)) {
        throw new RuntimeException("Incompability handler type: HadnlerInterface implementation required!");
      }
    }

    return new self($collection);
  }

  /**
   * Call handler in collection: 
   *  - you can just use string index of event (if it not required params);
   *  - if event require param you should provide it like a array ['index_of_event' => ...data]
   *
   * @param string|array<string, mixed> ...$params
   * @return void
   */
  function __invoke(mixed ...$params): void
  {
    foreach ($params as $paramsGroup) {
      foreach($paramsGroup as $index => $value) {
        if (is_string($index)) {
          if (is_array($value)) {
            $this->callHandlerWithId($index, ...$value);  
          } else {
            $this->callHandlerWithId($index, $value);
          }
        } elseif(is_string($value)) {
          $this->callHandlerWithId($value);
        }
      }
    }
  }

  private function callHandlerWithId(int|string $id, mixed ...$params): void
  {
    $handler = $this->getHandler($id);

    if ($handler) {
      call_user_func($handler, ...$params);
    }
  }

  function setHandler(HandlerInterface $callback, int|string $id): self
  {
    $this->collection[$id] = $callback;

    return $this;
  }

  function getHandler(int|string $id): HandlerInterface|false
  {
    return $this->collection[$id] ?? false;
  }
}