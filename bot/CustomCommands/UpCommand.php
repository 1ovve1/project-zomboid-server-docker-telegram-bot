<?php 

namespace PZBot\CustomCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\TelegramLog;
use PZBot\Commands\AdminCommand;
use PZBot\Database\User;
use PZBot\Exceptions\Checked\CheckedException;
use PZBot\Server\Manager;

class UpCommand extends AdminCommand
{
    /**
     * @var string
     */
    protected $name = 'up';

    /**
     * @var string
     */
    protected $description = 'Up server';

    /**
     * @var string
     */
    protected $usage = '/up';

    /**
     * @var string
     */
    protected $version = '1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        try {
            Manager::up();
        } catch (CheckedException $e) {
            return $this->replyToChat($e->getMessage());    
        }
    
        return $this->replyToChat("Server was up! This may take a several minutes!");
    }
}
