<?php

namespace App\Utilities;

use App\Database\Db;
use App\Models\Order;
use Exception;
use InvalidArgumentException;

class Query
{
  private static $query;
  private static $table;

  public static function select(string $fields = "*")
  {
    // https://stackoverflow.com/questions/125268/chaining-static-methods-in-php
    self::$query .= "SELECT $fields FROM ";
    return new self;
  }

  public static function delete()
  {
    self::$query .= "DELETE FROM ";
    return new self;
  }

  public static function insert()
  {
    self::$query .= "INSERT INTO ";
    return new self;
  }

  public static function update()
  {
    self::$query .= "UPDATE ";
    return new self;
  }

  public static function set(array $array)
  {
    $string = "";
    foreach ($array as $key => $value) {
      $string .= $key . " = " . $value . ", ";
    }
    $string = rtrim($string, ", ");
    self::$query .= "SET $string";
    return new self;
  }

  public static function values(string $values = "")
  {
    self::$query .= "VALUES ($values)";
    return new self;
  }

  public static function fields(string $fields = "")
  {
    self::$query .= "($fields) ";
    return new self;
  }

  public function table(string $table)
  {
    self::$table = $table;
    self::$query .= "$table ";
    return new self;
  }

  public function where(string $field, string $operator, $value)
  {

    if (!in_array($operator, ['=', '==', '>', '<', '<=', '>=', 'LIKE'])) {
      throw new InvalidArgumentException("Operator $operator not allowed.");
    }
    if (str_contains(self::$query, "WHERE")) {
      if (is_numeric($value))
        self::$query .= " AND $field $operator $value ";
      else
        self::$query .= " AND $field $operator '$value' ";
    } else {
      if (is_numeric($value))
        self::$query .= " WHERE $field $operator $value ";
      else
        self::$query .= " WHERE $field $operator '$value' ";
    }
    return new self;
  }

  public function and(string $field, string $operator, $value)
  {
    if (!in_array($operator, ['=', '>', '<', '<=', '>=', 'LIKE'])) {
      throw new InvalidArgumentException("Operator $operator not allowed.");
    }
    self::$query .= "AND $field $operator $value ";
    return new self;
  }

  public function join(string $table, string $field1, string $operator, string $field2)
  {
    if (!in_array($operator, ['='])) {
      throw new InvalidArgumentException("Operator $operator not allowed.");
    }
    self::$query .= "JOIN $table ON $field1 $operator $field2 ";
    return new self;
  }

  public function limit(int $num)
  {
    self::$query .= "LIMIT $num ";
    return new self;
  }

  public function get()
  {
    try {
      $conn = DB::connectDB();
      $result = $conn->query(self::$query);

      $array = [];
      while ($row = $result->fetch_object()) {
        $array[] = $row;
      }
      self::$query = "";
      return $array;
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }

  public function getArray()
  {
    try {
      $conn = DB::connectDB();
      $result = $conn->query(self::$query);

      $array = [];
      while ($row = $result->fetch_assoc()) {
        $array[] = $row;
      }
      self::$query = "";
      return $array;
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }

  public function getQuery()
  {
    $query = self::$query;
    self::$query = "";
    return $query;
  }

  public function getModel()
  {
    $array = $this->get();
    $object = $this->findModelName();
    $object = "\App\Models\\$object";
    $object = new $object();

    if ($array == null)
      return false;

    foreach ($array[0] as $key => $value) {
      $object->$key = $value;
    }

    return $object;
  }

  public function getModels()
  {
    $models = [];
    $array = $this->get();
    $object = $this->findModelName();
    $object = "\App\Models\\$object";

    if ($array == null)
      return false;

    foreach ($array as $stdClass) {
      $model = new $object();
      foreach ($stdClass as $key => $value) {
        $model->$key = $value;
      }
      $models[] = $model;
    }

    return $models;
  }

  private function findModelName()
  {
    $table = self::$table;
    return ucfirst(rtrim($table, "s"));
  }
}
