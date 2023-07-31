<?php 

namespace PZBot\Commands;

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Entities\User as EntitiesUser;

use PZBot\Database\User;
use PZBot\Server\Commands\Bash\BashCommandResolver;
use PZBot\Server\Commands\Factories\ExecutorFactory;
use PZBot\Server\Factories\ManagerFactoryInterface;
use PZBot\Server\Factories\StatusManagerFactory;
use PZBot\Server\ManagerInterface;

abstract class AdminCommand extends SystemCommand
{
    protected ?EntitiesUser $user;
    protected ManagerFactoryInterface $managerFactory;

    function __construct(Telegram $telegram, ?Update $update = null)
    {
        $this->managerFactory = new StatusManagerFactory(
            new ExecutorFactory(
                new BashCommandResolver
            )
        );

        parent::__construct($telegram, $update);
    }

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

    function getManager(): ManagerInterface
    {
        return $this->managerFactory->getManager();
    }
}
