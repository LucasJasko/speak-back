<?php

// header('Content-Type: text/plain');

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);


require_once "../class/Src/App.php";

use \Core\Router\Router;

\Src\App::init();
// TODO gérer toute les routes finissant par /

// =================== BACK-OFFICE ROUTES ============================== //

Router::add("/", function ($isApi = false) {
  $controller = new Src\Controller\Profile();
  $controller->dispatch();
});

Router::add("/login", function ($isApi = false) {
  $controller = new Src\Controller\Login();
  $controller->dispatch($isApi);
});

Router::add("/logout/{id}", function ($id, $isApi = false) {
  $controller = new Src\Controller\Logout();
  $controller->dispatch($id, $isApi);
});

Router::add("/group", function ($isApi = false) {
  $controller = new Src\Controller\Group();
  $controller->dispatch();
});

Router::add("/group/{id}", function ($id, $isApi = false) {
  $controller = new Src\Controller\Group();
  $controller->dispatch($id, $isApi);
});

Router::add("/room/{group_id}/{room_id}", function ($group_id, $room_id, $isApi = false) {
  $controller = new Src\Controller\Room();
  $controller->dispatch($group_id, $room_id, $isApi);
});

Router::add("/profile", function () {
  $controller = new Src\Controller\Profile();
  $controller->dispatch();
});

Router::add("/profile/{id}", function ($id, $isApi = false) {
  $controller = new Src\Controller\Profile();
  $controller->dispatch($id, $isApi);
});

Router::add("/params", function ($isApi = false) {
  $controller = new Src\Controller\Params();
  $controller->dispatch();
});

Router::add("/params/{tab}/{id}", function ($tab, $id, $isApi = false) {
  $controller = new Src\Controller\Params();
  $controller->dispatch($tab, $id, $isApi);
});

Router::add("/stats", function ($isApi = false) {
  $controller = new Src\Controller\Stats();
  $controller->dispatch($isApi);
});

Router::add("/delete/{table_name}/{id}/{redirect_page}/{delete_key}", function ($tableName, $id, $redirectpage, $deleteKey, $isApi = false) {
  $controller = new Src\Model\Form($tableName, $redirectpage);
  $controller->delete($deleteKey, $id, $isApi);
});

Router::add("/error", function ($isApi = false) {
  \Src\Auth\Auth::protect();
  require_once "../pages/error.php";
});

Router::add("/page404", function () {
  require_once "../pages/page404.php";
});

// =================== API ROUTES ============================== //

Router::add("/socket", function ($isApi = false) {
  $controller = new \Src\Api\Socket();
  $controller->dispatch($isApi);
});

Router::add("/auth", function ($isApi = false) {
  $controller = new \Src\Api\Auth();
  $controller->dispatch($isApi);
});

Router::add("/key", function ($isApi = false) {
  $controller = new \Src\Api\PublicKey();
  $controller->dispatch($isApi);
});

Router::add("/theme", function ($isApi = false) {
  $controller = new \Src\Api\Theme();
  $controller->dispatch($isApi);
});

Router::add("/rooms", function ($isApi = false) {
  $controller = new \Src\Api\Room();
  $controller->dispatch($isApi);
});

Router::add("/edit-profile", function ($isApi = false) {
  $controller = new \Src\Api\EditProfile();
  $controller->dispatch($isApi);
});

Router::add("/chat/{action}", function ($action, $isApi = false) {
  $controller = new \Src\Api\Chat();
  $controller->dispatch($action, $isApi);
});
Router::add("/dm/{id}", function ($id, $isApi = false) {
  $controller = new \Src\Controller\Dm();
  $controller->dispatch($id, $isApi);
});

Router::add("/search/{subject}", function (string $subject, $isApi = false) {
  $controller = new \Src\Api\Search();
  $controller->dispatch($subject, $isApi);
});

Router::add("/profile-groups/{profile_id}", function ($id, $isApi = false) {
  $controller = new \Src\Api\ProfileGroups();
  $controller->dispatch($id, $isApi);
});

Router::add("/group-profiles", function ($isApi = false) {
  $controller = new \Src\Api\GroupProfiles();
  $controller->dispatch($isApi);
});

Router::add("/image/{table}/{folder_name}/{subfolder}/{file_name}", function ($table, $folderName, $subfolder, $fileName, $isApi = false) {
  $controller = new \Src\Api\Image();
  $controller->dispatch($table, $folderName, $subfolder, $fileName, $isApi);
});

Router::dispatch($path);

exit();