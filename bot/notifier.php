<?php

use PZBot\Database\ServerStatus;
use PZBot\Server\Status;

$serverIsUp = fn() => trim(shell_exec("sudo docker container inspect -f '{{.State.Running}}' project-zomboid-server-docker_ProjectZomboidDedicatedServer_1"));
$serverIsActive = fn() => shell_exec('grep "Chat server successfully initialized." ./data/Logs/*.txt');

if ($serverIsUp() === "true") {

  if ($serverIsActive()) {
    sleep(2);
    if ($serverIsActive()) {
      ServerStatus::updateStatus(Status::ACTIVE);
    }
  } else {
    if (!ServerStatus::isRestarted()) {
      ServerStatus::updateStatus(Status::PENDING);
    }
  }
  
} else {
  ServerStatus::updateStatus(Status::DOWN);
}