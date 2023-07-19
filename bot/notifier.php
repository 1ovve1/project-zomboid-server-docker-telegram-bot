<?php

use Longman\TelegramBot\Request;
use PZBot\Database\ServerStatus;
use PZBot\Server\Status;

$serverIsUp = fn() => shell_exec("sudo docker container inspect -f '{{.State.Running}}' project-zomboid-server-docker_ProjectZomboidDedicatedServer_1");
$serverIsActive = fn() => shell_exec('grep "Chat server successfully initialized." ./data/Logs/*.txt');

if ($serverIsUp()) {

  if ($serverIsActive) {
    ServerStatus::updateStatus(Status::ACTIVE);
  } else {
    if (!ServerStatus::isRestarted()) {
      ServerStatus::updateStatus(Status::PENDIGN);
    }
  }
  
} else {
  ServerStatus::updateStatus(Status::DOWN);
}