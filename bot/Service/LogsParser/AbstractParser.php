<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;

use PZBot\Exceptions\Checked\LogsFilePremissionDeniedException;
use PZBot\Exceptions\Checked\LogsFileWasNotFoundedException;
use PZBot\Service\LogsParser\DTO\UniqueDTOInterface;

abstract class AbstractParser implements ParserInterface
{
  /**
   * @var array<ParserOptionsEnum>
   */
  protected readonly array $options;
  /**
   * @var integer|null - read lines limit
   */
  protected ?int $limit = null;
  /**
   * @var array<string>
   */
  protected array $collection = [];

  /**
   * Create instance
   *
   * @param ParserOptionsEnum ...$options
   * @return self
   * @throws LogsFileWasNotFoundedException
   * @throws LogsFilePremissionDeniedException
   */
  static function create(ParserOptionsEnum ...$options): self
  {
    return new static(...$options);
  }

  /**
   * @param ParserOptionsEnum ...$options
   * @throws LogsFileWasNotFoundedException
   * @throws LogsFilePremissionDeniedException
   */
  function __construct(ParserOptionsEnum ...$options)
  {
    $this->options = $options;
  }

  /**
   * Check option in options array field
   *
   * @param ParserOptionsEnum $option
   * @return boolean
   */
  private function isOption(ParserOptionsEnum $option): bool
  {
    return in_array($option, $this->options);
  }

  /**
   * Parse data and return array
   *
   * @return array
   * @throws LogsFilePremissionDeniedException
   * @throws LogsFileWasNotFoundedException
   */
  function parse(): array
  {
    $file = new File($this->getFilePath());

    $generator = match($this->isOption(ParserOptionsEnum::FROM_TOP)) {
      true => $file->openReverse(),
      default => $file->open()
    };

    foreach ($generator as $counter => $line) {
      preg_match($this->getRegExp(), $line, $matches);

      if (empty($matches)) {
        continue;
      }

      $dto = $this->matchHandler($matches);
      $this->resolveData($dto);

      if ($this->isOption(ParserOptionsEnum::ONCE) || $this->limitReached($counter)) {
        break;
      }
    }

    return $this->getCollection();
  }

  /**
   * Resolve data by options
   *
   * @param UniqueDTOInterface $dto
   * @return void
   */
  function resolveData(UniqueDTOInterface $dto): void
  {
    if ($this->isOption(ParserOptionsEnum::UNIQUE)) {
      $this->addCollectionUnique($dto);
    } else { 
      $this->addCollection($dto);
    }
  }

  /**
   * Add DTO into collection
   * Check if this DTO unique
   *
   * @param UniqueDTOInterface $dto
   * @return void
   */
  protected function addCollectionUnique(UniqueDTOInterface $dto): void
  {
    $key = $dto->getId();

    if (!key_exists($key, $this->collection)) {
      $this->addCollection($dto, $key);
    }
  }

  /**
   * @param UniqueDTOInterface $dto
   * @param string|integer|null|null $key
   * @return void
   */
  protected function addCollection(UniqueDTOInterface $dto, string|int|null $key = null): void
  {
    if (is_null($key)) {
      $this->collection[] = $dto;
    } else {
      $this->collection[$key] = $dto;
    }
  }

  /**
   * @return array<string>
   */
  protected function getCollection(): array
  {
    return $this->collection;
  }

  /**
   * Set limit by value
   *
   * @param integer $limit
   * @return self
   */
  function setLimit(int $limit): self
  {
    $this->limit = $limit;
    return $this;
  }

  /**
   * Check if limit reached (require counter)
   *
   * @param integer $counter
   * @return boolean
   */
  protected function limitReached(int $counter): bool
  {
    return ($this->limit ?? PHP_INT_MAX) < $counter;
  }
}