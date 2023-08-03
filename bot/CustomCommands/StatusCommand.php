<?php 

namespace PZBot\CustomCommands;

use DateTime;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use PZBot\Commands\AbstractCommand;
use PZBot\Database\ServerStatus;
use PZBot\Exceptions\Checked\LogsFilePremissionDeniedException;
use PZBot\Exceptions\Checked\LogsFileWasNotFoundedException;
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
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $serverStatus = $this->getServerStatus();
        $playersStatus = $this->getPlayersStatus();
        

        return $this->replyToChat(sprintf(
            "%s\n\n%s",
            $serverStatus, $playersStatus
        ));
    }

    public function getServerStatus(): string
    {
        $status = ServerStatus::getLastStatus();
    
        return sprintf(
            "🤖 Server status: %s\n🏠 IP: %s\n🕳 Port: %d\n㊙ Password: %s",
            $status->withSmile(), 
            $this->appConfig->get("HOST_IP", "unknown"), 
            $this->appConfig->get("PORT", "unknown"), 
            $this->appConfig->get("PASSWORD", "No"),
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

            var_dump($activity->activityTime);

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
