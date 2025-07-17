<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="shortcut icon" href="../assets/img/Speak_32x32.png" type="image/x-icon">
  <title>SPEAK ADMIN - Page 404</title>
</head>

<body>
  <?php if (isset($_SESSION["auth_key"])) { ?>
    <div class="header">
      <div class="logo-container">
        <img class="speak-logo" src="../assets/img/Speak_64x64.png" alt="logo de l'application Speak">
        <span class="logo-span">SPEAK</span>
      </div>
      <div class="title-container">
        <h1>Tableau de bord</h1>
      </div>
    </div>

    <?php require_once ROOT . "/pages/template/navbar.php";
  } ?>

  <div class="param-window error-window">
    <h2>Page 404</h2>
    <p>OUPS... la page recherchée n'existe pas</p>
    <?php if (isset($_SESSION["auth_key"])) { ?>
      <a href="profile">Vers les profiles</a>
    <?php } else { ?>
      <a href="login">Vers l'accueil</a>
    <?php } ?>
  </div>

  <script src="js/index.js"></script>
</body>

</html>