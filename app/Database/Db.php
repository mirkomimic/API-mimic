<?php

namespace App\Database;

use mysqli;

class Db
{
  protected static $conn;

  public static function connectDB()
  {
    if (self::$conn == null)
      self::$conn = new mysqli('localhost', 'root', '', 'zadatak_nbsoft');
    return self::$conn;
  }
}
