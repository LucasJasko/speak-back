<?php

namespace Src\Service;

class ImageApi extends Image
{
  private $imageType;
  private $id;

  public function __construct($type)
  {
    $this->imageType = $type;
  }

  public function createApiPicture($table, $b64image, array $profileData, $id = "")
  {
    $this->id = $id != "" ? $id : $_POST[$table . "_id"];

    preg_match('/^data:image\/(\w+);base64,/', $b64image, $matches);
    $extension = $matches[1];

    if (in_array($extension, ["jpg", "jpeg", "png", "gif", "webp"])) {

      $this->deleteExistingImages($table);

      $filename = "speak-" . $table . "-" . $this->id;

      $this->pathFile .= $table . "/" . $this->id . "-speak-" . $table . "/";

      if (!is_dir($this->pathFile)) {
        mkdir($this->pathFile);
      }
      if (is_dir($this->pathFile) && !is_dir($this->pathFile . "/" . $this->imageType)) {
        mkdir($this->pathFile . "/" . $this->imageType);
      }

      $this->pathFile .= $this->imageType . "/";

      $profileData[$table . "_picture"] = $filename;

      $this->createApiIMG($b64image, $table, $filename, $extension);

    }

    if (file_exists($this->pathFile . $filename . "." . $extension)) {
      unlink($this->pathFile . $filename . "." . $extension);
    }

    \Src\App::db()->setPicture($table, $table . "_picture", $table . "_id", "$filename.webp", $this->id);
  }

  private function createAPiIMG($b64img, $table, $filename, $extension)
  {
    $filename = $this->cleanFileName($filename);
    $filename = $this->incrementFileName($filename);

    if (preg_match('/^data:image\/\w+;base64,/', $b64img)) {
      $b64img = preg_replace('/^data:image\/\w+;base64,/', '', $b64img);
    }
    $imageData = base64_decode($b64img);
    file_put_contents($this->pathFile . $filename . '.' . $extension, $imageData);

    $srcPrefix = "";
    $srcExtension = $extension;

    foreach (IMG_CONFIG[$this->imageType] as $prefix => $info) {

      $srcSize = getimagesize($this->pathFile . $srcPrefix . $filename . "." . $srcExtension);

      $srcWidth = $srcSize[0];
      $srcHeight = $srcSize[1];

      $srcX = 0;
      $srcY = 0;

      $destX = 0;
      $destY = 0;

      $destWidth = $info["width"];
      $destHeight = $info["height"];

      if (!$info["crop"]) {

        if ($srcWidth > $srcHeight) {
          $destHeight = round(($srcHeight * $destWidth) / $srcWidth);

          if ($srcWidth <= $destWidth) {
            $destWidth = $srcWidth;
            $destHeight = $srcHeight;
          }

        } else {
          $destWidth = round(($srcWidth * $destHeight) / $srcHeight);

          if ($srcWidth <= $destWidth) {
            $destWidth = $srcWidth;
            $destHeight = $srcHeight;
          }
        }

      } else {

        // On vérifie que l'image est au format paysage
        if ($srcWidth > $srcHeight) {
          $srcX = round(($srcWidth - $srcHeight) / 2);
          $srcWidth = $srcHeight;

          // Sinon elle est au format portrait
        } else {
          $srcY = round(($srcHeight - $srcWidth) / 2);
          $srcHeight = $srcWidth;
        }

      }

      $dest = imagecreatetruecolor($destWidth, $destHeight);

      $src = ("imagecreatefrom" . str_replace("jpg", "jpeg", $srcExtension))($this->pathFile . $srcPrefix . $filename . "." . $srcExtension);
      // On effectue une copie de l'image uploadé
      imagecopyresampled($dest, $src, $destX, $destY, $srcX, $srcY, $destWidth, $destHeight, $srcWidth, $srcHeight);

      // Et on l'enregistre au format webp
      imagewebp($dest, $this->pathFile . $prefix . ($this->pathFile == $table . "_picture" ? "_" : "") . $filename . ".webp", 100);

      $srcExtension = "webp";

      // On ne change le préfix pour l'image suivante que si l'image qu'on vient de traiter n'est pas une image rogné.
      if (!$info["crop"]) {
        $srcPrefix = $prefix . "_";
      }

    }
  }
}