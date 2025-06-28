<?php

namespace Core\Database;

use \PDO;
use PDOException;

class Database
{
  private string $dbHost;
  private string $dbName;
  private string $dbUsername;
  private string $dbPassword;

  protected $db;
  public function __construct(string $dbhost, string $dbname, string $dbuser, string $dbpass)
  {
    $this->dbHost = $dbhost;
    $this->dbName = $dbname;
    $this->dbUsername = $dbuser;
    $this->dbPassword = $dbpass;

    $this->init();
  }

  public function init()
  {
    $dsn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName . ';charset=utf8';
    try {
      $this->db = new PDO($dsn, $this->dbUsername, $this->dbPassword);
    } catch (PDOException $e) {
      return "Erreur de connexion : " . $e->getMessage();
    }
    return $this->db;
  }

  public function lastInsertId()
  {
    return $this->db->lastInsertId();
  }

  public function createOne(string $table, array $data, array $fields)
  {
    $sql = "INSERT INTO `" . $table . "` ( ";
    foreach ($fields as $key => $value) {
      $sql .= $value . ", ";
    }
    $sql .= ") VALUES ( ";
    foreach ($fields as $value) {
      $sql .= ":" . $value . ", ";
    }
    $sql .= ")";
    $sql = str_replace(", ) VALUES", " ) VALUES", $sql);
    $sql = str_replace(", )", " )", $sql);

    $stmt = $this->db->prepare($sql);

    foreach ($data as $key => $value) {
      if ($key != $table . "_id") {
        $stmt->bindValue(":" . $key, $value);
      }
    }
    $stmt->execute();
    return $this->db->lastInsertId();
  }

  public function updateOne(string $table, array $data, string $param, int $id)
  {
    $sql = "UPDATE `" . $table . "` SET ";
    foreach ($data as $key => $value) {
      $sql .= $key . " = :" . $key . ", ";
    }
    $sql .= "WHERE " . $param . " = :" . $param;
    $sql = str_replace(", WHERE", " WHERE", $sql);

    $stmt = $this->db->prepare($sql);
    foreach ($data as $key => $value) {
      $stmt->bindValue(":" . $key, $value);
    }
    $stmt->bindValue(":" . $param, $id);

    return $stmt->execute();
  }

  public function getAll(string $table)
  {
    $id = $table . "_id";
    $stmt = $this->db->prepare("SELECT * FROM `" . $table . "` ORDER BY " . $id . " ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllWhere(string $table, string $field, mixed $value)
  {
    $stmt = $this->db->prepare("SELECT * FROM `" . $table . "` WHERE " . $field . " = :" . $field);
    $stmt->execute([":" . $field => $value]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllWhereOr(string $table, string $field, array $value)
  {
    $stmt = "SELECT * FROM `" . $table . "` WHERE ";

    for ($i = 0; $i < count($value); $i++) {
      if ($i < count($value) - 1) {
        $stmt .= "$field = :" . $field . "_" . $i . " OR ";
      } else {
        $stmt .= "$field = :" . $field . "_" . $i;
      }
    }

    $stmt = $this->db->prepare($stmt);

    for ($i = 0; $i < count($value); $i++) {
      $stmt->bindValue(":" . $field . "_" . $i, $value[$i]);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllWhereAnd(string $table, string $field1, string $field1Value, string $field2, string $field2Value)
  {
    $stmt = $this->db->prepare("SELECT * FROM `" . $table . "` WHERE " . $field1 . " = :" . $field1 . " AND " . $field2 . " = :" . $field2);
    $stmt->bindValue(":" . $field1, $field1Value);
    $stmt->bindValue(":" . $field2, $field2Value);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getOneWhere(string $table, string $field, mixed $value)
  {
    $stmt = $this->db->prepare("SELECT * FROM `" . $table . "` WHERE " . $field . " = :" . $field);
    $stmt->execute([":" . $field => $value]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getFieldWhere(string $table, string $target, string $field, $value)
  {
    $stmt = $this->db->prepare("SELECT " . $target . " FROM `" . $table . "` WHERE " . $field . " = :" . $field);
    $stmt->execute([":" . $field => $value]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }


  public function getOneWhereAnd(string $table, string $field1, string $field1Value, string $field2, string $field2Value)
  {
    $stmt = $this->db->prepare("SELECT * FROM `" . $table . "` WHERE " . $field1 . " = :" . $field1 . " AND " . $field2 . " = :" . $field2);
    $stmt->bindValue(":" . $field1, $field1Value);
    $stmt->bindValue(":" . $field2, $field2Value);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getFieldWhereAnd(string $table, string $target, string $field1, string $field1Value, string $field2, string $field2Value)
  {
    $stmt = $this->db->prepare("SELECT " . $target . " FROM `" . $table . "` WHERE " . $field1 . " = :" . $field1 . " AND " . $field2 . " = :" . $field2);
    $stmt->bindValue(":" . $field1, $field1Value);
    $stmt->bindValue(":" . $field2, $field2Value);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getFieldsWhereAnd(string $table, array $target, string $field1, string $field1Value, string $field2, string $field2Value)
  {
    $sql = "SELECT ";
    for ($i = 0; $i < count($target); $i++) {
      if ($i != count($target) - 1) {
        $sql .= $target[$i] . ", ";
      } else {
        $sql .= $target[$i];
      }
    }
    $sql .= " FROM `" . $table . "` WHERE " . $field1 . " = :" . $field1 . " AND " . $field2 . " = :" . $field2;
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":" . $field1, $field1Value);
    $stmt->bindValue(":" . $field2, $field2Value);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getField(string $table, string $field)
  {
    $stmt = $this->db->prepare("SELECT " . $field . " FROM `" . $table . "` ORDER BY " . $field . " ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getFieldsWhere(string $table, array $target, string $field, $value)
  {
    $sql = "SELECT ";
    for ($i = 0; $i < count($target); $i++) {
      if ($i != count($target) - 1) {
        $sql .= $target[$i] . ", ";
      } else {
        $sql .= $target[$i];
      }
    }
    $sql .= " FROM `" . $table . "` WHERE " . $field . " = :" . $field;
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":" . $field => $value]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getMultipleWhere(string $table, array $target, string $field, $value)
  {
    $sql = "SELECT ";
    for ($i = 0; $i < count($target); $i++) {
      if ($i != count($target) - 1) {
        $sql .= $target[$i] . ", ";
      } else {
        $sql .= $target[$i];
      }
    }
    $sql .= " FROM `" . $table . "` WHERE " . $field . " = :" . $field;
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":" . $field => $value]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getFieldsOfTable(string $table)
  {
    $stmt = $this->db->prepare('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = "' . $table . '" AND TABLE_SCHEMA = "' . DB_NAME . '"');
    $stmt->execute();
    $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $fields = [];
    for ($i = 0; $i < count($raw); $i++) {
      foreach ($raw[$i] as $k => $v) {
        $fields[$i] = $v;
      }
    }

    return $fields;
  }

  public function deleteOne(string $table, string $field, int $param)
  {
    $stmt = $this->db->prepare("DELETE FROM `" . $table . "` WHERE " . $field . " = :" . $field);
    $stmt->execute([":" . $field => $param]);
  }

  public function deleteAllWhere(string $table, string $field, string $fieldValue)
  {
    $stmt = $this->db->prepare("DELETE FROM `" . $table . "` WHERE " . $field . " = :" . $field);
    $stmt->bindValue(":" . $field, $fieldValue);
    $stmt->execute();
  }

  public function deleteAllWhereAnd(string $table, string $field1, string $field1Value, string $field2, string $field2Value)
  {
    $stmt = $this->db->prepare("DELETE FROM `" . $table . "` WHERE " . $field1 . " = :" . $field1 . " AND " . $field2 . " = :" . $field2);
    $stmt->bindValue(":" . $field1, $field1Value);
    $stmt->bindValue(":" . $field2, $field2Value);
    $stmt->execute();
  }
}
