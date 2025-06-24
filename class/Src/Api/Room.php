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

      $rooms = App::db()->getAllWhere("room", "group_id", $req["group"]);

      for ($i = 0; $i < count($rooms); $i++) {
        $rooms[$i]["id"] = $rooms[$i]["room_id"];
        $rooms[$i]["name"] = $rooms[$i]["room_name"];
        $rooms[$i]["icon"] = "";
        $rooms[$i]["groupID"] = $rooms[$i]["group_id"];

        unset($rooms[$i]["room_id"]);
        unset($rooms[$i]["room_name"]);
        unset($rooms[$i]["room_creation_time"]);
        unset($rooms[$i]["room_delete_time"]);
        unset($rooms[$i]["state_id"]);
        unset($rooms[$i]["type_id"]);
      }

      App::sendApiData($rooms);

    }
  }
}