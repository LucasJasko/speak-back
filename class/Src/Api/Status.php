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

      $id = App::getApiData();

      App::sendApiData($id);

    }
  }
}