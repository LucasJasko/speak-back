<?php

namespace Src\Controller;

abstract class Controller extends \Core\Controller\Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function clearRecordset($recordset, $tab)
  {
    for ($i = 0; $i < count($recordset); $i++) {
      $clearedRecordset[$i] = $recordset[$i][$tab . "_id"];
    }
    return $clearedRecordset;
  }

  public function getModelsFromRecordset(array $recordset, string $modelName)
  {
    for ($i = 0; $i < count($recordset); $i++) {
      $id = $recordset[$i];
      $modelPath = "Src\Model\Entity\\" . ucfirst($modelName);
      $modelInstance = new $modelPath($id);
      $models[$i] = $modelInstance->all();
    }
    return $models;
  }

  public function unsetFieldsToNotRender(array $fields, array $fieldsToNotRender)
  {
    if (count($fieldsToNotRender) != 0) {
      foreach ($fieldsToNotRender as $index => $field) {
        if ($fields[$field]) {
          unset($fields[$field]);
        }
      }
    }
    return $fields;
  }

  public function getDashboard($pageName, $paramsConfig = [], $dashboardInfos = [], $fieldsToNotRender = [])
  {

    if ($pageName == "profile" || $pageName == "group") {
      $recordset = $this->db->getField($pageName, $pageName . "_id");
      if ($recordset != []) {
        $clearedRecordset = $this->clearRecordset($recordset, $pageName);
        $models = $this->getModelsFromRecordset($clearedRecordset, ucfirst($pageName));
      }
      $fields = $this->unsetFieldsToNotRender($dashboardInfos, $fieldsToNotRender);
      $data = isset($models) ? $models : [];
      $tab = $pageName;
      $page = $pageName;

      require_once ROOT . "/pages/" . $pageName . ".php";
      return;
    }

    foreach ($paramsConfig as $table => $v) {
      $recordset = $this->db->getField($table, $table . "_id");
      $clearedRecordset = $this->clearRecordset($recordset, $table);

      for ($i = 0; $i < count($clearedRecordset); $i++) {
        $recordsets[$table] = $this->getModelsFromRecordset($clearedRecordset, $table);
        $ParamsFields[$table][] = $this->db->getFieldsOfTable($table);
      }
    }

    require_once ROOT . "/pages/params.php";
  }
}
