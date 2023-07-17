<?php 

namespace PZBot\Commands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\TelegramLog;
use PZBot\Database\User;

abstract class AdminCommand extends SystemCommand
{

    /**
     * Main command execution
     *
     * @throws TelegramException
     */
    public function preExecute(): ServerResponse
    {
        $user = $this->getMessage()->getFrom();
        
        TelegramLog::info("User try to restart server", $user->getRawData());

        if (User::isNotAdmin($user->getId())) {
            throw new TelegramException("User is not admin");
        }

        return parent::preExecute();
    }
}
