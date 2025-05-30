<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="shortcut icon" href="../assets/img/Speak_32x32.png" type="image/x-icon">
  <title>SPEAK ADMIN - Gestion des paramètres</title>
</head>

<body>

  <div class="header">
    <div class="logo-container">
      <img class="speak-logo" src="../assets/img/Speak_64x64.png" alt="logo de l'application Speak">
      <span class="logo-span">SPEAK</span>
    </div>
    <div class="title-container">
      <h1>Tableau de bord - Gestion des paramètres de l'application</h1>
    </div>
  </div>


  <?php require_once ROOT . "/pages/template/navbar.php" ?>

  <main class="main-container">

    <div class="params-container">

      <?php
      foreach ($paramsConfig as $table => $infos):
        $fields = $dashboardInfos[$table];
        $data = $recordsets[$table];
        $tab = $table;
        $page = "params";
        ?>

        <div class="param-window param-window-small <?= $infos["field_name"] ?>">
          <h2 class="param-title"><?= $infos["field_desc"] ?></h2>
          <div class="btn-container">
            <a class="valid-button add-button" href="params/<?= $infos["field_name"] ?>/0">Ajouter
              <?= $infos["field_p"] ?></a>
          </div>
          <?php require ROOT . "/pages/template/dashboard.php" ?>
        </div>
      <?php endforeach ?>

    </div>

    <div class="delete-container"></div>
  </main>

  <input type="text" class="target" value="Paramétrages" hidden>

  <script src="js/index.js"></script>
  <script src="js/params.js"></script>
</body>

</html>