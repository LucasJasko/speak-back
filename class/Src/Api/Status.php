<?php

namespace Src\Api;

use \Src\App;

class Status
{
  public function get($isApi)
  {

    if ($isApi) {

      if (!\Src\Api\Auth::protect()) {
        http_response_code(403);
        exit();
      }

      $status_list = App::db()->getAll("status");

      App::sendApiData($status_list);
    }
  }

  public function put($isApi)
  {
    if ($isApi) {

      if (!\Src\Api\Auth::protect()) {
        http_response_code(403);
        exit();
      }

      $statusId = App::getApiData()["statusId"];
      $userId = App::getApiData()["id"];

      if (App::db()->updateOne("profile", ["status_id" => $statusId], "profile_id", $userId)) {
        App::sendApiData("statut bien modifié");
      } else {
        App::sendApiData("échec de la modification du statut");
      }

    }
  }
}