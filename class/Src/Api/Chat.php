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

            if ($dmId = App::db()->getDmBetweeenAandB("dm", "profile_id_A", $infos["target"], "profile_id_B", $infos["origin"])[0]["dm_id"]) {

              $messageIds = App::db()->getFieldsWhere("message__dm", ["message_id"], "dm_id", $dmId);

              if (!empty($messageIds)) {

                $idList = [];
                for ($i = 0; $i < count($messageIds); $i++) {
                  $idList[$i] = $messageIds[$i]["message_id"];
                }

                $feed = App::db()->get50Messages("message_id", $idList, 0);

                if ($feed) {

                  for ($i = 0; $i < count($feed); $i++) {

                    $clearedMessage = [
                      "messageHeaders" => [
                        "isForGroup" => false,
                        "date" => $feed[$i]["message_creation_time"],
                        "type" => "message",
                        "sender" => $feed[$i]["profile_id"],
                        "target" => $feed[$i]["profile_id"] == intval($infos["origin"]) ? intval($infos["target"]) : intval($infos["origin"]),
                      ],
                      "messageBody" => [
                        "text" => $feed[$i]["message_content"],
                      ]
                    ];

                    $feed[$i] = $clearedMessage;
                  }

                  App::sendApiData($feed);

                } else {
                  App::sendApiData([]);
                }

              } else {
                App::sendApiData([]);
              }
            }
          }

          break;

        case "message":

          $message = App::getApiData()["pendingMessage"];

          if ($message["messageHeaders"]["target"] != "0") {

            $target = $message["messageHeaders"]["target"];
            $sender = $message["messageHeaders"]["sender"];
            $file = isset($message["messageBody"]["file"]["name"]) ? htmlspecialchars($message["messageBody"]["file"]["name"]) : "";
            $text = isset($message["messageBody"]["text"]) ? htmlspecialchars($message["messageBody"]["text"]) : "";
            $dmId = App::db()->getDmBetweeenAandB("dm", "profile_id_A", $target, "profile_id_B", $sender)[0]["dm_id"];

            $dbMessage = [
              "message_file" => $file,
              "message_content" => $text,
              "message_creation_time" => date('Y-m-d H:i:s', time()),
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