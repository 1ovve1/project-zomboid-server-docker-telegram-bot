<?php declare(strict_types=1);

namespace PZBot\Database;
use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;

class PicturesHistory extends QueryBuilder implements MigrateAble
{
  /**
   * Return list of pictures in picture history store by key
   *
   * @param string $key
   * @return array
   */
  static function getAllPicturesHistoryByKey(string $key): array
  {
    $queryResult = self::select(["picture_name"])
      ->where(["key"], $key)
      ->save();

    return $queryResult->fetchCollumn();
  }

  /**
   * @param string $key
   * @param string $picture_name
   * @return void
   */
  static function insertPictureByKey(string $key, string $picture_name): void
  {
    self::insert([
      "key" => $key,
      "picture_name" => $picture_name
    ])->save();
  }

  /**
   * Wipe all pictures history with key
   *
   * @param string $key
   * @return void
   */
  static function deletePicturesHistoryByKey(string $key): void
  {
    self::delete()
      ->where(["key"], $key)
      ->save();
  }
  

  static function migrationParams(): array 
  {
    return [
      'fields' => [
        'id' => "BIGINT PRIMARY KEY AUTO_INCREMENT",
        'key' => "VARCHAR(50)",
        'picture_name' => "VARCHAR(255)",
      ]
    ];
  }
}