<?php

namespace Src\Model\Entity;

class Room extends \Src\Model\Model
{

  private int $id;
  private string $name;
  private $state;
  private $type;
  private $group;

  protected static array $formInfos = [
    "room_id" => [
      "label" => "Identifiant du salon",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required readonly"
    ],
    "room_creation_time" => [
      "label" => "Date de création du salon",
      "placeholder" => "",
      "input_type" => "date",
      "attributes" => ""
    ],
    "room_delete_time" => [
      "label" => "Date de suppression du salon",
      "placeholder" => "",
      "input_type" => "date",
      "attributes" => ""
    ],
    "group_id" => [
      "label" => "Groupe du salon",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required readonly"
    ],
    "room_name" => [
      "label" => "Nom du salon",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required"
    ],
    "state_id" => [
      "label" => "Etat du salon",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required"
    ],
    "type_id" => [
      "label" => "Type du salon",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required"
    ],
  ];

  public function __construct($id, $newData = [])
  {
    $this->tableName = "room";
    $this->searchField = "room_id";

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

  protected function hydrate($row, $table)
  {
    foreach ($row as $key => $value) {
      if (str_contains($key, "room_")) {
        $key = str_replace("room_", "", $key);
      }
      if (!str_contains($key, "room_")) {
        $key = str_replace("_id", "", $key);
      }
      $method = "set" . $key;
      if (method_exists($this, $method)) {
        $this->{$method}($value);
      }
    }
  }

  public function submitModel(array $data)
  {

    if (empty($data["room_id"])) {
      $data["room_creation_time"] = date('Y-m-d H:i:s', time());
      $this->createNewModel("room", $data);
      return;
    }

    $this->updateModel($data["room_id"], $data);
  }

  public function deleteModel()
  {
    try {
      $this->db->deleteOne($this->tableName, $this->searchField, $this->id);
      \core\Service\Log::writeLog("Le salon " . $this->id() . " : " . $this->name() . " a été supprimé de la base de donnée.");
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
  public function setState(int $stateID)
  {
    $instance = new State($stateID);
    $this->state = $instance->name();
  }
  public function setType(int $typeID)
  {
    $instance = new Type($typeID);
    $this->type = $instance->name();
  }
  public function setGroup(int $groupID)
  {
    $instance = new Group($groupID);
    $this->group = $instance->name();
  }


  public function all()
  {
    return [
      "room_id" => $this->id(),
      "room_name" => $this->name(),
      "state_id" => $this->state(),
      "type_id" => $this->type(),
      "group_id" => $this->group()
    ];
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
  public function state()
  {
    return htmlspecialchars($this->state);
  }
  public function type()
  {
    return htmlspecialchars($this->type);
  }
  public function group()
  {
    return htmlspecialchars($this->group);
  }
  public static function formInfos()
  {
    return self::$formInfos;
  }
}
