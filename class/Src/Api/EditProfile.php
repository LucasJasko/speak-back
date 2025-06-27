<?php

namespace Src\Api;

use \Src\App;

class EditProfile
{
  public function dispatch($isApi)
  {

    if ($isApi) {

      if (!\Src\Api\Auth::protect()) {
        return http_response_code(403);
      }

      $req = App::getApiData();

      if ($req["param"]) {
        switch ($req["param"]) {
          case "mail":

            if ($req["pwd"] && $pwd = App::db()->getFieldWhere("profile", "profile_password", "profile_id", $req["id"])["profile_password"]) {

              if (password_verify($req["pwd"], $pwd)) {

                if (App::db()->updateOne("profile", ["profile_mail" => $req["new"]], "profile_id", $req["id"])) {
                  App::sendApiData(200);
                } else {
                  App::sendApiData(500);
                }
              } else {
                App::sendApiData(403);
              }
            }

            break;
        }
      }

    }
  }
}