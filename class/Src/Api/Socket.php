<?php

namespace Src\Api;

class Socket
{
  private $socketServer;
  private array $connections;
  private array $members;
  private string $address = "0.0.0.0";
  private int $port = 8061;

  public function dispatch($isApi = false)
  {

    if ($isApi) {

      \Src\App::sendApiData(\Src\Api\Auth::protect());

    } else {
      http_response_code(400);
    }

  }
  // TODO créer une méthode de ping, qui vérifie si l'utilisateur est toujours présent, et déconnextion du socket si non
  public function lauchSocketServer()
  {

    if ($this->socketServer === null) {

      $this->socketServer = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
      socket_bind($this->socketServer, $this->address, $this->port);
      socket_listen($this->socketServer);

    }

    $this->listenForNewConnections($this->socketServer);

  }
  private function listenForNewConnections($sock)
  {
    $this->members = [];
    $this->connections[] = $sock;

    echo "Le serveur écoute sur le port " . $this->port . "\n";

    while (true) {

      $reads = $this->connections;
      $writes = $exceptions = null;
      socket_select($reads, $writes, $exceptions, 0, 1000000);
      $this->acceptNewConnections($sock, $reads);
      $this->handleIncomingMessage($reads);

    }
  }
  private function acceptNewConnections($sock, $reads)
  {
    if (in_array($sock, $reads, 0)) {

      $new_connections = socket_accept($sock);
      $header = socket_read($new_connections, 1024);

      $this->handshake($header, $new_connections);
      $this->connections[] = $new_connections;

      $sock_index = array_search($sock, $reads);
      unset($reads[$sock_index]);

    }
  }
  private function handleIncomingMessage($reads)
  {
    foreach ($reads as $key => $sock) {

      // On ne renvoie rien au serveur lui même
      if ($sock === $this->socketServer) {
        continue;
      }

      $data = socket_read($sock, 1024);

      if (!empty($data)) {

        $message = $this->unmask($data);

        $decoded_message = json_decode($message, true);

        // TODO TRES IMPORTANT !! ajouter + de vérification du format de message avant envoie (pour éviter les éventuelles modifications intermédiaires donc htmlspecialchars sur les champs modifiables)

        if ($decoded_message && isset($decoded_message["messageHeaders"]["type"])) {

          $type = $decoded_message["messageHeaders"]["type"];

          switch ($type) {

            case "join":

              echo "Client " . $key . " connecté \n";

              $this->members[$key] = [
                "member_id" => $decoded_message["messageHeaders"]["sender"],
                "isForGroup" => $decoded_message["messageHeaders"]["isForGroup"],
                "listening" => $decoded_message["messageHeaders"]["target"],
                "connection" => $sock,
              ];

              break;

            case "message":

              $masked_message = $this->pack_data($message);


              $decoded_message["messageBody"]["text"] = htmlspecialchars($decoded_message["messageBody"]["text"] ?? '');

              foreach ($this->members as $mkey => $mvalue) {
                // Si le membre n'est pas l'expéditeur du message
                if ($decoded_message["messageHeaders"]["sender"] != $mvalue["member_id"]) {
                  // Si le membre est bien le destinataire du message
                  if ($decoded_message["messageHeaders"]["target"] === $mvalue["member_id"]) {
                    // Si le membre destinataire est bien en train d'écouter l'expéditeur
                    if ($mvalue["listening"] === $this->members[$key]["member_id"]) {
                      socket_write($mvalue["connection"], $masked_message, strlen($masked_message));
                    }

                  }
                }
              }

              break;

            case "switch":

              $this->members[$key]["listening"] = $decoded_message["messageHeaders"]["target"];
              $this->members[$key]["isForGroup"] = $decoded_message["messageHeaders"]["isForGroup"];

              if ($this->members[$key]["isForGroup"]) {
                echo "Profile " . $this->members[$key]["member_id"] . " écoute Salon " . $this->members[$key]["listening"] . "\n";
              } else {
                echo "Profile " . $this->members[$key]["member_id"] . " écoute Profile " . $this->members[$key]["listening"] . "\n";
              }

              break;

            case "close":
              break;

            case "error":
              break;

          }
        }

      } else if ($data === '' || !$data) {

        echo "Client " . $key . " déconnecté \n";
        unset($this->connections[$key]);
        unset($this->members[$key]);
        socket_close($sock);
      }
    }
  }
  private function unmask($payload)
  {
    // La recommandation RFC6455 stipule qu'un payload doit avoir une longueur de 7 bits, on doit donc effectuer la conversion de 8 à 7.
    $length = @ord($payload[1]) & 127;

    if ($length == 126) {
      $masks = substr($payload, 4, 4);
      $data = substr($payload, 8);
    } else if ($length == 127) {
      $masks = substr($payload, 10, 4);
      $data = substr($payload, 14);
    } else {
      $masks = substr($payload, 2, 4);
      $data = substr($payload, 6);
    }

    $unmasked = "";
    for ($i = 0; $i < strlen($data); ++$i) {
      $unmasked .= $data[$i] ^ $masks[$i % 4];
    }

    return $unmasked;
  }
  private function pack_data($text)
  {
    // 0x80 = 128, 0x1 = 1, 0x0f = 15
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($text);

    if ($length <= 125) {
      $header = pack("CC", $b1, $length);
    } else if ($length > 125 && $length < 65536) {
      $header = pack("CCn", $b1, 126, $length);
    } else if ($length >= 65536) {
      $header = pack("CCNN", $b1, 127, 0, $length);
    }
    return $header . $text;

  }
  private function handshake($request_header, $sock)
  {
    $headers = [];
    $lines = preg_split("/\r\n/", $request_header);

    foreach ($lines as $line) {
      $line = chop($line);
      if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
        $headers[$matches[1]] = $matches[2];
      }
    }

    if (!isset($headers['Sec-WebSocket-Key'])) {
      socket_close($sock);
      return;
    }

    $sec_key = $headers['Sec-WebSocket-Key'];
    $sec_accept = base64_encode(pack('H*', sha1($sec_key . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11")));
    $response_header =
      "HTTP/1.1 101 Switching Protocols\r\n" .
      "Upgrade: websocket\r\n" .
      "Connection: Upgrade\r\n" .
      "Sec-WebSocket-Accept:$sec_accept\r\n\r\n";

    socket_write($sock, $response_header, strlen($response_header));
  }
}