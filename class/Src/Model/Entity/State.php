<?php

namespace Src\Model\Entity;

class State extends \Src\Model\Model
{

  private int $id;
  private string $name;
  protected static array $formInfos = [
    "state_id" => [
      "label" => "Identifiant de l'état",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required readonly"
    ],
    "state_name" => [
      "label" => "Nom de l'état",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required"
    ],
  ];

  protected static array $dashboardInfos = [
    "state_id" => "ID",
    "state_name" => "Nom",
  ];


  public function __construct(int $id, $newData = [])
  {
    $this->tableName = "state";
    $this->searchField = "state_id";
    $this->initdb($this->tableName, $this->searchField);
    $row = $this->db->getOneWhere($this->tableName, $this->searchField, $id);

    if ($row) {
      if (count($row) != 0) {
        $this->hydrate($row, $this->tableName);
      }
    } else {
      $this->hydrate($newData, $this->tableName);
    }
  }

  public function deleteModel()
  {
    try {
      $this->db->deleteOne($this->tableName, $this->searchField, $this->id);
      \core\Service\Log::writeLog("L'état " . $this->id() . " : " . $this->name() . " a été supprimé de la base de donnée.");
    } catch (\PDOException $e) {
      return $e;
    }
  }

  public function setId(int $id)
  {
    $this->id = $id;
  }
  public function setName(string $name)
  {
    $this->name = $name;
  }
  public function setTableName($tableName)
  {
    $this->tableName = $tableName;
  }
  public function setSearchField($searchField)
  {
    $this->searchField = $searchField;
  }

  public function id()
  {
    return htmlspecialchars($this->id);
  }
  public function name()
  {
    return htmlspecialchars($this->name);
  }
  public function tableName()
  {
    return htmlspecialchars($this->tableName);
  }
  public function searchField()
  {
    return htmlspecialchars($this->searchField);
  }
  public static function formInfos()
  {
    return self::$formInfos;
  }
  public static function dashboardInfos()
  {
    return self::$dashboardInfos;
  }
}
