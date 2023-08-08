<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;

use PZBot\Service\LogsParser\DTO\UniqueDTOInterface;

abstract class AbstractParser implements ParserInterface
{
  /**
   * @var File
   */
  protected readonly File $file;
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
    $this->file = new File(
      $this->getFilePath()
    );
  }

  /**
   * Parse file data using options
   *
   * @return array
   */
  private function getFileData(): array
  {
    if ($this->isOption(ParserOptionsEnum::FROM_TOP)) {
      return array_reverse($this->file->data);
    } 

    return $this->file->data;
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
   */
  function parse(): array
  {
    $data = $this->getFileData();

    foreach ($data as $counter => $line) {
      preg_match($this->getRegExp(), $line, $mathces);

      if (empty($mathces)) {
        continue;
      }

      $dto = $this->matchHandler($mathces);
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