<?php

namespace Src\Model\Entity;

class Group extends \Src\Model\Model
{
  private int $id;
  private string $name;
  private string $picture;
  private $state;
  private $type;
  private array $room;

  // TODO faire une classe général Entity plutôt que Model

  protected static array $formInfos = [
    "group_id" => [
      "label" => "Identifiant du groupe",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required readonly"
    ],
    "group_name" => [
      "label" => "Nom du groupe",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required"
    ],
    "group_picture" => [
      "label" => "Image du groupe",
      "placeholder" => "",
      "input_type" => "file",
      "attributes" => ""
    ],
    "state_id" => [
      "label" => "Etat du groupe",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required"
    ],
    "type_id" => [
      "label" => "Type de groupe",
      "placeholder" => "",
      "input_type" => "text",
      "attributes" => "required"
    ],
    "room_id" => [
      "label" => "Salons du groupe",
      "placeholder" => "",
      "input_type" => "select",
      "attributes" => "required"
    ]
  ];

  protected static array $dashboardInfos = [
    "group_id" => "ID",
    "group_name" => "Nom",
    "state_id" => "Etat",
    "type_id" => "Type"
  ];

  public function __construct(int $id, $newData = [])
  {
    $this->id = $id;
    $this->tableName = "group";
    $this->searchField = "group_id";

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
      if (str_contains($key, "group_")) {
        $key = str_replace("group_", "", $key);
      } else {
        $key = str_replace("_id", "", $key);
      }
      $method = "set" . ucfirst($key);
      if (method_exists($this, $method)) {
        $this->{$method}($value);
      }
    }
    $this->setRoom($this->id);
  }

  public function deleteModel()
  {
    try {
      $this->db->deleteOne($this->tableName, $this->searchField, $this->id);
      \core\Service\Log::writeLog("Le groupe " . $this->id() . " : " . $this->name() . " a été supprimé de la base de donnée.");
    } catch (\PDOException $e) {
      return $e;
    }
  }

  public function submitModel(array $data)
  {
    if (empty($data["group_id"])) {

      $data["group_creation_time"] = time();

      $lastInsertId = $this->createNewModel("group", $data);

      $imageManager = new \Src\Service\Image("profile_picture");
      return $imageManager->createPicture("group", $lastInsertId);
    }

    $imageManager = new \Src\Service\Image("profile_picture");
    $imageManager->createPicture("group");

    $this->updateModel($data["group_id"], $data);
  }

  public function all()
  {
    return [
      "group_id" => $this->id(),
      "group_name" => $this->name(),
      "group_picture" => $this->picture(),
      "state_id" => $this->state(),
      "type_id" => $this->type(),
      "room_id" => $this->room()
    ];
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
  public function setRoom(int $groupID)
  {
    $res = $this->db->getAllWhere("room", "group_id", $groupID);
    $this->room = $res;
  }
  public function setId(int $id)
  {
    $this->id = $id;
  }
  public function setName(string $name)
  {
    $this->name = $name;
  }
  public function setPicture(string|null $picture)
  {
    if ($picture == null) {
      $this->picture = "default.webp";
    } else {
      $this->picture = $picture;
    }
  }
  public function setTableName($tableName)
  {
    $this->tableName = $tableName;
  }
  public function setSearchField($searchField)
  {
    $this->searchField = $searchField;
  }

  public static function formInfos()
  {
    return self::$formInfos;
  }
  public static function dashboardInfos()
  {
    return self::$dashboardInfos;
  }
  public function id()
  {
    return htmlspecialchars($this->id);
  }
  public function name()
  {
    return htmlspecialchars($this->name);
  }
  public function picture()
  {
    return htmlspecialchars($this->picture);
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
  public function room()
  {
    return $this->room;
  }

}
