<?php

namespace Src\Api;

use \Src\App;

class Theme
{

  public function dispatch($isApi)
  {

    if ($isApi) {

      if (!\Src\Api\Auth::protect()) {
        http_response_code(403);
        exit();
      }

      $req = App::getApiData();

      $themeId = App::db()->getOneWhere("theme", "theme_name", $req["theme"])["theme_id"];
      if (App::db()->updateOne("profile", ["theme_id" => $themeId], "profile_id", $req["id"])) {
        http_response_code(200);
      } else {
        http_response_code(500);
      }


    }
  }

}