<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser\DTO;

/**
 * Needs if we want collect unique DTO objects while reading logs 
 * (maybe we want seeing only last logs records)
 */
interface UniqueDTOInterface
{
  /**
   * Return id of this object
   *
   * @return integer
   */
  function getId(): int;
}