<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="shortcut icon" href="/../assets/img/Speak_32x32.png" type="image/x-icon">
  <title>Tableau de bord - Formulaire de modification</title>
</head>

<body>

  <main class="form__container">

    <h1><?= $metaInfos["form_title"] ?></h1>


    <form class="form" action="/<?= $metaInfos["redirect_page"] ?>" method="post" enctype="multipart/form-data">

      <a class="return-link" href="/<?= $metaInfos["return_page"] ?>"><i class="fa-solid fa-arrow-left"></i></a>

      <?php
      foreach ($displayedData as $dataField => $dataValue) {

        $label = $fieldsInfos[$dataField]["label"];
        $inputType = $fieldsInfos[$dataField]["input_type"];
        $placeholder = $fieldsInfos[$dataField]["placeholder"];
        $attributes = $fieldsInfos[$dataField]["attributes"];
        ?>

        <label for="<?= $dataField ?>"> <?= $label ?> :</label>

        <?php
        if (str_contains($dataField, $metaInfos["table_name"])) {
          ?>
          <input type='<?= $inputType ?>' placeholder='<?= $placeholder ?>' name="<?= $dataField ?>" id="<?= $dataField ?>"
            <?= !empty($dataValue) ? "value='" . $dataValue . "'" : "" ?>     <?= $attributes ?>>

          <?php
        } else {

          if (is_array($dataValue)) {

            switch ($dataField) {

              case "situation_id":

                empty($dataValue) ? $dataValue[] = [["" => ""]] : "";
                for ($index = 0; $index < count($dataValue); $index++):

                  foreach ($dataValue[$index] as $post => $department): ?>

                    <div class="dbl-select__container">

                      <select class="dbl-select" name="situation_id[<?= $index ?>][post_id]">
                        <option value="">-- Poste --</option>

                        <?php
                        $options = \Src\Model\Form::getDataOfTable("post");
                        for ($i = 0; $i < count($options); $i++):
                          ?>

                          <option value="<?= $options[$i]["post_id"] ?>" <?= $options[$i]["post_name"] == $post ? "selected" : "" ?>>
                            <?= $options[$i]["post_name"] ?>
                          </option>

                        <?php endfor ?>

                      </select>

                      <select class="dbl-select" name="situation_id[<?= $index ?>][department_id]">

                        <option value="">-- Département --</option>

                        <?php
                        $options = \Src\Model\Form::getDataOfTable("department");

                        for ($i = 0; $i < count($options); $i++): ?>

                          <option value="<?= $options[$i]["department_id"] ?>" <?= $options[$i]["department_name"] == $department ? "selected" : "" ?>><?= $options[$i]["department_name"] ?></option>

                        <?php endfor ?>

                      </select>

                    </div>

                    <?php
                  endforeach;
                endfor ?>

                <button class="valid-button plus-btn ">Ajouter une situation</button>

                <?php break;

              case "room_id": ?>

                <div class="edit-select__container">

                  <select class="edit-select">

                    <option value="">-- Sélectionnez un salon à éditer --</option>

                    <?php for ($i = 0; $i < count($dataValue); $i++): ?>
                      <option value="<?= $dataValue[$i]["room_id"] ?>">
                        <?= $dataValue[$i]["room_name"] ?>
                      </option>
                    <?php endfor ?>

                  </select>

                  <a class="valid-button edit-button" href=""> <i class='fa-solid fa-pen'></i></a>
                </div>

                <a class="valid-button add-room-btn new-room" href="/room/<?= $displayedData["group_id"] ?>/0">Créer un nouveau
                  salon</a>

                <script>
                  const editSelect = document.querySelector(".edit-select");
                  const editButton = document.querySelector(".edit-button");
                  const groupID = document.querySelector("#group_id").value;

                  editSelect.addEventListener("change", () => {
                    editButton.href = "/room/" + groupID + "/" + editSelect.value;
                    if (editSelect.value == "") {
                      editButton.style.backgroundColor = "grey";
                    }

                  });
                </script>

                <?php break;
            }
          } else {
            if (str_contains("group_id", $dataField) && isset($fieldsInfos["form_fields"]["room_name"])) {
              ?>
              <input type='<?= $inputType ?>' name="<?= $dataField ?>" id="<?= $dataField ?>"
                value="<?= $groupName["group_name"] ?>" disabled <?= $attributes ?>>
              <input type='<?= $inputType ?>' name="<?= $dataField ?>" id="<?= $dataField ?>" value="<?= $linkedId ?>" hidden
                <?= $attributes ?>>

            <?php } else {

              $options = \Src\Model\Form::getDataOfTable(str_replace("_id", "", $dataField));

              if ($dataField == "group_id" && array_key_exists("room_id", $fieldsInfos)) {
                for ($i = 0; $i < count($options); $i++) {
                  if ($options[$i]["group_id"] == $metaInfos["linked_id"]) { ?>

                    <input type='<?= $inputType ?>' value="<?= $options[$i]["group_name"] ?>" readonly>
                    <input type='<?= $inputType ?>' name="<?= $dataField ?>" id="<?= $dataField ?>"
                      value="<?= $options[$i]["group_id"] ?>" required hidden>

                  <?php }
                }
              } else { ?>
                <select name="<?= $dataField ?>">

                  <?php

                  for ($i = 0; $i < count($options); $i++) {

                    $fieldName = str_replace("_id", "_name", $dataField);
                    ?>

                    <option value="<?= $options[$i][$dataField] ?>" <?= $options[$i][$fieldName] == $displayedData[$dataField] ? "selected" : "" ?>>
                      <?= $options[$i][$fieldName] ?>
                    </option>

                  <?php } ?>

                </select>
              <?php }
            }
          }
        }
      }

      if ($metaInfos["redirect_page"] == "params"): ?>

        <input class=" table" type="text" name="table_name" value="<?= $tableName ?>" hidden>

      <?php endif ?>

      <input class=" table" type="text" value="<?= $metaInfos["redirect_page"] ?>" hidden>
      <input class="valid-button" type="submit" value="Enregistrer">

      <?php
      if (str_contains($returnPage, "group/") && array_key_exists("room_id", $displayedData)):
        if ($displayedData["room_id"] != "0"): ?>
          <a class="valid-button delete-room"
            href="/delete/room/<?= $displayedData["room_id"] ?>/group/<?= $_SESSION["delete_key"] ?>">Supprimer
            le
            salon</a>
        <?php endif;
      endif ?>

    </form>

  </main>
</body>

<script src="/js/form.js"></script>

</html>