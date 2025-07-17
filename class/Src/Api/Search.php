<?php

namespace Src\Api;

use \Src\App;

class Search
{

  public function dispatch($subject, $isApi)
  {
    if ($isApi) {

      if (!\Src\Api\Auth::protect()) {
        http_response_code(403);
        exit();
      }

      switch ($subject) {

        case "profiles":
          if (!\Src\Api\Auth::protect()) {
            http_response_code(403);
            exit();
          }

          $req = App::getApiData();
          $res = App::db()->getResultsThatContain("profile", ["profile_name", "profile_id", "profile_surname", "profile_picture", "status_id"], "profile_name", "profile_surname", $req["query"]);
          App::sendApiData($res);
          break;

        case "email":

          $req = App::getApiData();

          if ($res = App::db()->getFieldWhere("profile", "profile_mail", "profile_mail", $req["query"])) {
            App::sendApiData(true);
          } else {
            App::sendApiData(false);
          }
          break;
      }
    } else {
      http_response_code(400);
    }
  }
}
