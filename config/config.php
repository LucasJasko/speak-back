<?php

define("ROOT", dirname(__DIR__));

define("DB_HOST", "localhost");
define("DB_NAME", "speak");
define("DB_USER", "root");
define("DB_PASS", "");

define("PAGE_LIST", [
  "group",
  "user",
  "params",
  "stats",
  "login"
]);

define("JWT_SECRET_KEY", "tropbetepourtrouver");

// define("VENDORS", [
//   "Firebase\JWT\JWT" => ROOT . "\\vendor\\firebase\php-jwt\src\JWT.php",
//   "Firebase\JWT\Key" => ROOT . "\\vendor\\firebase\php-jwt\src\Key.php"
// ]);

define("VENDORS", [
  "Firebase\JWT\JWT" => ROOT . "/vendor/firebase/php-jwt/src/JWT.php",
  "Firebase\JWT\Key" => ROOT . "/vendor/firebase/php-jwt/src/Key.php"
]);

define("IMG_CONFIG", [
  "profile_picture" => [
    "" => ["width" => 64, "height" => 64, "crop" => true]
  ],
  "profile_images" => [
    "lg" => ["width" => 1200, "height" => 900, "crop" => false],
    "md" => ["width" => 800, "height" => 600, "crop" => false],
    "sm" => ["width" => 200, "height" => 200, "crop" => true],
    "xs" => ["width" => 64, "height" => 64, "crop" => true]
  ]
]);