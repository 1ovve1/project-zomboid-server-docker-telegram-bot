<?php declare(strict_types=1);


use Longman\TelegramBot\TelegramLog;
use QueryBox\DBFacade;
use QueryBox\Migration\Container\QueryObject;
use QueryBox\Migration\MetaTable;
use PZBot\Database\ChatGptDialog;

require __DIR__ . "/bootstrap.php";

TelegramLog::info("Migrate bot tables");

$structureSqlPath = __DIR__ . '/structure.sql';

TelegramLog::info("Searching for '{$structureSqlPath}'");
if (file_exists($structureSqlPath)) {
  $structure = file_get_contents(__DIR__ . '/structure.sql'); 
} else {
  TelegramLog::ERROR("File '{$structureSqlPath}' not found!");
}

TelegramLog::info("Triyng to migrate bot tables...");
try {
  DBFacade::getDBInstance()->rawQuery((new QueryObject())->setRawSql($structure));
} catch (Exception $e) {
  TelegramLog::ERROR($e->getMessage());
}
TelegramLog::info("DONE!");

TelegramLog::info("Triyng to migrate other meta tables...");

$tables = [
  ChatGptDialog::class
];

try {
  $migrationTool = MetaTable::createImmutable(DBFacade::getDBInstance());

  foreach ($tables as $table) {
    TelegramLog::info("Migrate table '{$table::table()}' ...");
    $migrationTool->doMigrateFromMigrateAble($table);
    TelegramLog::info("DONE!");
  }
} catch (Exception $e) {
  TelegramLog::ERROR($e->getMessage());
}