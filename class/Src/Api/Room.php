<?php

namespace Src\Api;

use \Src\App;

class Room
{

  public function dispatch($isApi)
  {
    if ($isApi) {
      if (!\Src\Api\Auth::protect()) {
        http_response_code(403);
        exit();
      }

      $req = App::getApiData();
      $groupID = $req["group"];

      App::sendApiData($groupID);

    }
  }
}