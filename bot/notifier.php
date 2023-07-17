<?php

use Longman\TelegramBot\Request;
use PZBot\Database\ServerStatus;
use PZBot\Server\Status;

if (!ServerStatus::isActive()) {
  // update server status
  $serverIsUp = shell_exec('grep "Chat server successfully initialized." ./data/Logs/*.txt');
  if ($serverIsUp) {
    sleep(2);
  
    $serverIsUp = shell_exec('grep "Chat server successfully initialized." ./data/Logs/*.txt');
    
    if ($serverIsUp) {
      ServerStatus::updateStatus(Status::ACTIVE);
      
      Request::sendToActiveChats(
        'sendMessage',
        ["text" => "SERVER ACTIVE"],
        [
          'groups'      => true,
          'supergroups' => true,
          'channels'    => false,
          // 'users'       => true,
        ]
      );
    }
  }
}