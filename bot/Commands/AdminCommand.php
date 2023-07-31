<?php 

namespace PZBot\Commands;

use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Exception\TelegramException;

use PZBot\Database\User;
use PZBot\Server\Commands\Bash\BashCommandResolver;
use PZBot\Server\Commands\Factories\ExecutorFactory;
use PZBot\Server\Factories\ManagerFactoryInterface;
use PZBot\Server\Factories\StatusManagerFactory;
use PZBot\Server\ManagerInterface;

abstract class AdminCommand extends AbstractCommand
{
    protected ManagerFactoryInterface $managerFactory;

    function createHook(): void
    {
        $this->managerFactory = new StatusManagerFactory(
            new ExecutorFactory(
                new BashCommandResolver
            )
        );

        TelegramLog::warinig("User try to manage server", $this->user->getRawData());

        if (User::isNotAdmin($this->user->getId())) {
            throw new TelegramException("User is not admin");
        }

    }

    function getManager(): ManagerInterface
    {
        return $this->managerFactory->getManager();
    }
}
