<?php declare(strict_types=1);

namespace PZBot\Database;

use QueryBox\QueryBuilder\QueryBuilder;

/**
 * User model class
 */
class User extends QueryBuilder
{
  /**
   * Find user by id in table
   *
   * @param integer $id
   * @return array<int, array<string|int, mixed>>
   */
  public static function findUserByID(int $id): array
  {
    return User::findFirst("id", $id)->fetchAll();
  }

  /**
   * Check if user exist in config array
   *
   * @param int $id
   * @return boolean
   */
  public static function isAdmin(int $id): bool
  {
    $adminList = $_ENV["CONFIG"]["admins"];

    return in_array($id, $adminList);
  }

  public static function isNotAdmin(int $id): bool
  {
    return !self::isADmin($id);
  }
}