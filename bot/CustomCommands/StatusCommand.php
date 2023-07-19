<?php 

namespace PZBot\CustomCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use PZBot\Database\ServerStatus;

class StatusCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'status';

    /**
     * @var string
     */
    protected $description = 'Server status';

    /**
     * @var string
     */
    protected $usage = '/status';

    /**
     * @var string
     */
    protected $version = '1.0';

    /**
     * @var bool
     */
    protected $private_only = false;

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
       $status = ServerStatus::getStatus();
    
        return $this->replyToChat(sprintf(
            "🤖 Server status: %s\n🏠 IP: %s\n🕳 Port: %d\n㊙ Password: %s",
            $status->withSmile(), $_ENV["HOST_IP"], $_ENV["PORT"], $_ENV["PASSWORD"] ?? "No"
        ));
    }
}
