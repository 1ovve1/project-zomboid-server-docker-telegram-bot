<?php 

namespace PZBot\CustomCommands;

use DateTime;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use PZBot\Commands\AbstractCommand;
use PZBot\CustomCommands\Middleware\AutoDeleteMessagesMiddleware;
use PZBot\Database\ChatMessagesHistory;
use PZBot\Database\ServerStatus;
use PZBot\Exceptions\Checked\LogsFilePremissionDeniedException;
use PZBot\Exceptions\Checked\LogsFileWasNotFoundedException;
use PZBot\Helpers\TelegramRequestHelper;
use PZBot\Service\LogsParser\DTO\UserActivityObject;

class StatusCommand extends AbstractCommand
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
     * @inheritDoc
     */
    function middleware(): array 
    {
        return [
            new AutoDeleteMessagesMiddleware
        ];
    }

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $serverStatus = $this->getServerStatus();
        $playersStatus = $this->getPlayersStatus();

        $response = $this->replyToChat(sprintf(
            "%s\n\n%s",
            $serverStatus, $playersStatus
        ));

        return $response;
    }

    public function getServerStatus(): string
    {
        $status = ServerStatus::getLastStatus();
    
        return sprintf(
            "🤖 Server status: %s\n🏠 IP: %s\n🕳 Port: %d\n㊙ Password: %s",
            $status->withSmile(), 
            env("HOST_IP", "unknown"),
            env("PORT", "unknown"),
            env("PASSWORD", "No"),
        );
    }

    public function getPlayersStatus(): string
    {
        $players = "🧟 Players:\n";

        try {
            $userStatusParser = $this->logsParserFactory->getUserStatusParser();
            
            /** @var array<UserActivityObject> $lastPzUsersActivities */
            $lastPzUsersActivities = $userStatusParser->parse();
        } catch (LogsFilePremissionDeniedException|LogsFileWasNotFoundedException) {
            return '';
        }

        $count = 0;
        foreach ($lastPzUsersActivities as $activity) {
            $daysAgo = (new DateTime())->diff($activity->activityTime)->d;

            if ($daysAgo < 7) {
                $players .= sprintf(
                    "\t%d) %s\n", 
                    ++$count, 
                    $activity->toString()
                );
            }
        }
        
        return $players;
    }
}
