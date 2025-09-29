<?php

namespace Src\Model;

abstract class Model
{
  protected $db;
  protected $tableName;
  protected $searchField;
  protected $data;

  public function initdb(string $tableName, string $searchField)
  {
    $this->db = \Src\App::db();

    $this->searchField = $searchField;
    $this->tableName = $tableName;
  }

  public function createNewModel(string $table, array $data)
  {
    $fields = $this->db->getFieldsOfTable($table);

    foreach ($fields as $key => $value) {
      if (str_contains($value, $this->tableName . "_id")) {
        unset($data[$key]);
        unset($fields[$key]);
      } else if (str_contains($value, $this->tableName . "_delete_time")) {
        unset($data[$key]);
        unset($fields[$key]);
      } else if (!isset($data[$value])) {
        $data[$value] = "";
      }
    }

    return $this->db->createOne($this->tableName, $data, $fields);
  }

  public function updateModel(int $id, array $newData)
  {
    $this->db->updateOne($this->tableName, $newData, $this->searchField, $id);
  }

  protected function hydrate($row, $table)
  {
    foreach ($row as $key => $value) {
      $method = "set" . ucfirst(str_replace($table . "_", "", $key));
      if (method_exists($this, $method)) {
        $this->{$method}($value);
      }
    }
  }
}
