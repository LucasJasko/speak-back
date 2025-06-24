<?php

namespace Src\Api;

use \Src\App;

class Chat
{
  public function dispatch($action, $isApi)
  {

    if ($isApi) {

      if (!\Src\Api\Auth::protect()) {
        return http_response_code(403);
      }

      switch ($action) {

        case "select":
          $req = App::getApiData();

          if ($targetProfile = App::db()->getOneWhere("profile", "profile_id", $req["target"])) {
            // TODO Ici à terme vérifier si l'utilisateur cible n'a pas bloqué l'utilisateur à l'origine de la requête

            if (empty(App::db()->getDmBetweeenAandB("dm", "profile_id_A", $req["target"], "profile_id_B", $req["origin"]))) {

              (new \Src\Model\Entity\Dm("0"))->submitModel(["dm_id" => "", "dm_creation_time" => "", "profile_id_A" => $req["target"], "profile_id_B" => $req["origin"], "state_id" => 1]);

              unset($targetProfile["profile_password"]);
              unset($targetProfile["profile_mail"]);
              unset($targetProfile["theme_id"]);
              unset($targetProfile["language_id"]);

              unset($targetProfile["profile_password"]);
              unset($targetProfile["profile_mail"]);
              unset($targetProfile["theme_id"]);
              unset($targetProfile["language_id"]);

              $targetProfile["creation"] = $targetProfile["profile_creation_time"];
              unset($targetProfile["profile_creation_time"]);

              $targetProfile["id"] = $targetProfile["profile_id"];
              unset($targetProfile["profile_id"]);

              $targetProfile["name"] = $targetProfile["profile_name"];
              unset($targetProfile["profile_name"]);

              $targetProfile["picture"] = $targetProfile["profile_picture"];
              unset($targetProfile["profile_picture"]);

              $targetProfile["surname"] = $targetProfile["profile_surname"];
              unset($targetProfile["profile_surname"]);

              $targetProfile["role"] = $targetProfile["role_id"];
              unset($targetProfile["role_id"]);

              $targetProfile["status"] = $targetProfile["status_id"];
              unset($targetProfile["status_id"]);

              App::sendApiData($targetProfile);

            } else {
              // TODO Envoyer à terme ici les infos du dm
              http_response_code(204);
            }

          } else {
            App::sendApiData("La cible n'est pas un utilisateur");
          }

          break;

        case "remove":
          //  TODO gérer la suppression d'une conversation privée
          break;


        case "messages":

          $infos = App::getApiData();

          if (isset($infos["target"]) & isset($infos["origin"])) {

            if ($res = App::db()->getDmBetweeenAandB("dm", "profile_id_A", $infos["target"], "profile_id_B", $infos["origin"])) {

              $dmId = $res[0]["dm_id"];
              $messageIds = App::db()->getFieldsWhere("message__dm", ["message_id"], "dm_id", $dmId);
              $targetFullName = App::db()->getFieldsWhere("profile", ["profile_name", "profile_surname", "profile_picture"], "profile_id", $infos["target"])[0];
              $originFullName = App::db()->getFieldsWhere("profile", ["profile_name", "profile_surname", "profile_picture"], "profile_id", $infos["origin"])[0];

              if (!empty($messageIds)) {

                $idList = [];

                for ($i = 0; $i < count($messageIds); $i++) {
                  $idList[$i] = $messageIds[$i]["message_id"];
                }

                $feed = App::db()->getAllWhereOr("message", "message_id", $idList);

                if ($feed) {

                  for ($i = 0; $i < count($feed); $i++) {

                    $clearedMessage = [];
                    foreach ($feed[$i] as $key => $value) {
                      $newKey = str_replace("message_", "", $key);
                      $clearedMessage[$newKey] = $value;
                    }


                    if ($clearedMessage["profile_id"] == $infos["target"]) {

                      $path = ROOT . "/upload/profile/" . $infos["target"] . "-speak-profile-" . strtolower($targetFullName["profile_surname"]) . "-" . strtolower($targetFullName["profile_name"]) . "/profile_picture/" . $targetFullName["profile_picture"];

                      $clearedMessage["author_picture"] = base64_encode(file_get_contents($path));
                      $clearedMessage["author_name"] = $targetFullName["profile_name"];
                      $clearedMessage["author_surname"] = $targetFullName["profile_surname"];
                    } else {

                      $path = ROOT . "/upload/profile/" . $infos["origin"] . "-speak-profile-" . strtolower($originFullName["profile_surname"]) . "-" . strtolower($originFullName["profile_name"]) . "/profile_picture/" . $originFullName["profile_picture"];

                      $clearedMessage["author_picture"] = base64_encode(file_get_contents($path));
                      $clearedMessage["author_name"] = $originFullName["profile_name"];
                      $clearedMessage["author_surname"] = $originFullName["profile_surname"];
                    }

                    $feed[$i] = $clearedMessage;
                  }

                  App::sendApiData($feed);
                }

              } else {
                App::sendApiData([]);
              }
            }
          }

          break;

        case "message":

          $message = App::getApiData()["pendingMessage"];

          if ($message["messageInfos"]["target"] != "0") {

            $target = $message["messageInfos"]["target"];
            $sender = $message["messageInfos"]["sender"];
            $file = isset($message["authorMessage"]["messageFile"]["FileName"]) ? htmlspecialchars($message["authorMessage"]["messageFile"]["FileName"]) : "";
            $text = isset($message["authorMessage"]["messageText"]) ? htmlspecialchars($message["authorMessage"]["messageText"]) : "";
            $dmId = App::db()->getDmBetweeenAandB("dm", "profile_id_A", $target, "profile_id_B", $sender)[0]["dm_id"];

            $dbMessage = [
              "message_file" => $file,
              "message_content" => $text,
              "message_creation_time" => "",
              "profile_id" => intval($sender),
            ];

            $lastInsertId = App::db()->createOne("message", $dbMessage, ["message_file", "message_content", "message_creation_time", "profile_id"]);

            App::db()->createOne("message__dm", ["message_id" => $lastInsertId, "dm_id" => strval($dmId)], ["message_id", "dm_id"]);

            App::sendApiData("success");
          } else {
            App::sendApiData("fail");
          }

          break;
      }

    } else {
      http_response_code(400);
    }
  }
}