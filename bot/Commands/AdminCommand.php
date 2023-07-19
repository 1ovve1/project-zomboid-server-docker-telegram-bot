<?php 

namespace PZBot\Commands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\User as EntitiesUser;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\TelegramLog;
use PZBot\Database\User;

abstract class AdminCommand extends SystemCommand
{
    protected ?EntitiesUser $user;

    /**
     * Main command execution
     *
     * @throws TelegramException
     */
    public function preExecute(): ServerResponse
    {
        $this->user = $this->getMessage()->getFrom();
        
        TelegramLog::warinig("User try to manage server", $this->user->getRawData());

        if (User::isNotAdmin($this->user->getId())) {
            throw new TelegramException("User is not admin");
        }

        return parent::preExecute();
    }
}
