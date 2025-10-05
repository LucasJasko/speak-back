<?php

namespace Src\Controller;

class Params extends \Src\Controller\Controller
{

  public array $paramsConfig = [
    "post" => [
      "field_name" => "post",
      "class_name" => "Post",
      "field_desc" => "Postes des utilisateurs",
      "field_p" => "un poste",
    ],
    "department" => [
      "field_name" => "department",
      "class_name" => "Department",
      "field_desc" => "Départements de l'entreprise",
      "field_p" => "un département",
    ],
    "theme" => [
      "field_name" => "theme",
      "class_name" => "Theme",
      "field_desc" => "Thèmes de l'application",
      "field_p" => "un thème",
    ],
    "status" => [
      "field_name" => "status",
      "class_name" => "Status",
      "field_desc" => "Statuts d'activité des utilisateurs",
      "field_p" => "un statut",
    ],
    "role" => [
      "field_name" => "role",
      "class_name" => "Role",
      "field_desc" => "Rôles de l'application",
      "field_p" => "un role",
    ],
    "language" => [
      "field_name" => "language",
      "class_name" => "Language",
      "field_desc" => "Langues de l'application",
      "field_p" => "une langue",
    ],
  ];

  private $dashboardsInfos;
  private $dashboard;
  private $form;
  public $formsInfos;

  private $paramInstance;

  public function __construct()
  {
    parent::__construct();
    foreach ($this->paramsConfig as $table => $v) {
      $model = "\Src\Model\Entity\\" . ucfirst($table);
      $this->formsInfos[$table] = $model::formInfos();
      $this->dashboardsInfos[$table] = $model::dashboardInfos();
    }
  }

  public function dispatch($tab = null, $id = null, $isApi = false)
  {

    \Src\Auth\Auth::protect();

    if (isset($id) && isset($tab)) {

      $modelName = "Src\Model\Entity\\" . ucfirst($tab);
      $model = new $modelName($id);

      if ($_POST) {
        unset($_POST["table_name"]);
        $model->submitModel($_POST);
        \Src\App::redirect("params");
      }

      if ($id != 0) {
        $form = new \Src\Model\Form($tab, "params/$tab/$id", $this->formsInfos[$tab]);
        return $form->getForm($model->all(), "Modification: $tab N° $id", "group");
      }

      $fieldsOfTable = $this->db->getFieldsOfTable($tab);
      $fieldsOfTable = array_fill_keys($fieldsOfTable, "");

      $form = new \Src\Model\Form($tab, "params/$tab/0", $this->formsInfos[$tab]);
      return $form->getEmptyForm($fieldsOfTable, "Création: " . $tab, "params", [$tab . "_id"]);

    }
    $this->getDashboard("params", $this->paramsConfig, $this->dashboardsInfos, []);

  }
}
