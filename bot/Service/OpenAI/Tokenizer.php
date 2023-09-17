<?php declare(strict_types=1);

namespace PZBot\Service\OpenAI;
use GPT3Encoder\Gpt3Encoder;

class Tokenizer extends Gpt3Encoder
{
    function tokenCount(string $prompt): int
    {
        $tokenArray = $this->encode($prompt);

        return count($tokenArray);
    }
}