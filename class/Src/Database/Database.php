<?php

namespace Src\Database;

class Database extends \Core\Database\Database
{

  public function getPicture($table, $target, $field, $value): mixed
  {
    $sql = "SELECT $target FROM `$table` WHERE $field = :$field AND $target IS NOT NULL AND $target <> ''";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":$field", $value);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function getResultsThatContain($table, array $searched, $target, $target2, $value)
  {
    $sql = "SELECT ";
    for ($i = 0; $i < count($searched); $i++) {
      if ($i < count($searched) - 1) {
        $sql .= $searched[$i] . ", ";
      } else {
        $sql .= $searched[$i];
      }
    }
    $sql .= " FROM `$table` WHERE $target LIKE CONCAT('%', :query, '%') OR $target2 LIKE CONCAT('%', :query, '%')";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":query", $value);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function setPicture($table, $target, $field, $targetValue, $fieldValue)
  {
    $sql = "UPDATE `$table` SET $target = :$target WHERE $field = :$field";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":$target", $targetValue);
    $stmt->bindValue(":$field", $fieldValue);
    $stmt->execute();
  }

  public function get50Messages(string $field, array $value, $offset)
  {
    $sql = "SELECT * FROM `message` WHERE ";

    for ($i = 0; $i < count($value); $i++) {
      if ($i < count($value) - 1) {
        $sql .= "$field = :" . $field . "_" . $i . " OR ";
      } else {
        $sql .= "$field = :" . $field . "_" . $i;
      }
    }

    $sql .= " ORDER BY `message_id` ASC LIMIT 50 OFFSET " . $offset;

    $stmt = $this->db->prepare($sql);

    for ($i = 0; $i < count($value); $i++) {
      $stmt->bindValue(":" . $field . "_" . $i, $value[$i]);
    }

    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  // TODO méthod dédiée à la recherche de discussion privé existantes
  public function getDmBetweeenAandB(string $table, string $field1, string $field1Value, string $field2, string $field2Value)
  {
    $stmt = $this->db->prepare("SELECT * FROM `" . $table . "` WHERE (" . $field1 . " = :A AND " . $field2 . " = :B) OR (" . $field2 . " = :A AND " . $field1 . " = :B)");
    $stmt->bindValue(":A", $field1Value);
    $stmt->bindValue(":B", $field2Value);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getDmOfProfile(string $table, string $field1, string $field2, string $value)
  {
    $stmt = $this->db->prepare("SELECT * FROM `" . $table . "` WHERE (" . $field1 . " = :A OR " . $field2 . " = :A)");
    $stmt->bindValue(":A", $value);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}
