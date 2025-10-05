<?php

namespace Src\Api;

use \Src\App;

class GroupProfiles
{
  public function dispatch($isApi = false)
  {

    if ($isApi) {

      if (!\Src\Api\Auth::protect()) {
        http_response_code(403);
        exit();
      }

      $req = App::getApiData();

      if ($res = App::db()->getFieldsWhere("profile__group", ["profile_id"], "group_id", $req["group"])) {

        if (count($res) > 0) {

          $profileIds = [];
          for ($i = 0; $i < count($res); $i++) {
            $profileIds[$i] = $res[$i]["profile_id"];
          }

          if ($profiles = App::db()->getAllWhereOr("profile", "profile_id", $profileIds)) {


            for ($i = 0; $i < count($profiles); $i++) {

              $name = strtolower($profiles[$i]["profile_name"]);
              $surname = strtolower($profiles[$i]["profile_surname"]);

              $path = ROOT . "/upload/profile/" . $profiles[$i]["profile_id"] . "-speak-profile-" . $surname . "-" . $name . "/profile_picture/speak-profile-" . $surname . "-" . $name;

              if (file_exists($path)) {
                $profiles[$i]["picture"] = base64_encode(file_get_contents($path));
              } else {
                $path = ROOT . "/upload/default/default.webp";
                $profiles[$i]["picture"] = base64_encode(file_get_contents($path));
              }

              $profiles[$i]["id"] = $profiles[$i]["profile_id"];
              $profiles[$i]["creation"] = $profiles[$i]["profile_creation_time"];
              $profiles[$i]["name"] = $profiles[$i]["profile_name"];
              $profiles[$i]["surname"] = $profiles[$i]["profile_surname"];
              $profiles[$i]["status"] = $profiles[$i]["status_id"];
              $profiles[$i]["role"] = $profiles[$i]["role_id"];
              $profiles[$i]["language"] = $profiles[$i]["language_id"];

              unset($profiles[$i]["profile_picture"]);
              unset($profiles[$i]["profile_surname"]);
              unset($profiles[$i]["profile_name"]);
              unset($profiles[$i]["profile_creation_time"]);
              unset($profiles[$i]["profile_id"]);
              unset($profiles[$i]["profile_password"]);
              unset($profiles[$i]["profile_mail"]);
              unset($profiles[$i]["language_id"]);
              unset($profiles[$i]["theme_id"]);
              unset($profiles[$i]["role_id"]);
              unset($profiles[$i]["status_id"]);

            }

            App::sendApiData($profiles);
          }
        }
      } else {
        App::sendApiData([]);
      }

    }
  }
}