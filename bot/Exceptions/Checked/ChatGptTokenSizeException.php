<?php declare(strict_types=1);

namespace PZBot\Exceptions\Checked;
use PZBot\Exceptions\CheckedException;

class ChatGptTokenSizeException extends CheckedException
{
  const MESSAGE = "Token size limit! Expect %s tokens, but given %s!";

 public function __construct(int $tokenSizeLimitExcpect, int $tokenSizeLimitReal) 
 {
  $message = sprintf(self::MESSAGE, $tokenSizeLimitExcpect, $tokenSizeLimitReal);

  parent::__construct($message);
 }
}