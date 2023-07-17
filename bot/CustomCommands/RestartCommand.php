<?php 

namespace PZBot\CustomCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\TelegramLog;
use PZBot\Commands\AdminCommand;
use PZBot\Exceptions\ServerManageException;
use PZBot\Server\Manager;

class RestartCommand extends AdminCommand
{
    /**
     * @var string
     */
    protected $name = 'restart';

    /**
     * @var string
     */
    protected $description = 'Restart server';

    /**
     * @var string
     */
    protected $usage = '/restart';

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
        $user = $this->getMessage()->getFrom();
        
        TelegramLog::info("User try to restart server", $user->getRawData());

        try {
            Manager::restart();
        } catch (ServerManageException $e) {
            return $this->replyToChat($e->getMessage());    
        }
    
        return $this->replyToChat("Server was restarted! This may take a several minutes!");
    }
}
