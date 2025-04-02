<?php
session_start();

require 'vendor/autoload.php';

use Controllers\HomeController;
use Controllers\UserController;
use Controllers\MessageController;
use Database\Database;

$router = new AltoRouter();

// Si ton projet est dans "http://localhost:8888/CookinCrew/",
$router->setBasePath('/CookinCrew');

/** 
 * ROUTES
 */
// 1) Accueil (GET + POST) sur "/"
$router->map('GET', '/', function () {
    $db = Database::getInstance();
    $homeController = new HomeController($db);
    $homeController->index(); // Gère le listing ET le formulaire
});

$router->map('POST', '/', function () {
    $db = Database::getInstance();
    $homeController = new HomeController($db);
    $homeController->index(); // Gère la création du message en POST
});

// 2) INSCRIPTION
$router->map('GET', '/inscription', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->inscription();
});
$router->map('POST', '/inscription', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->inscription();
});

// 3) CONNEXION
$router->map('GET', '/connexion', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->index();
});
$router->map('POST', '/connexion', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->connexion();
});

// 4) ADMIN
$router->map('GET', '/admin', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->admin();
});

// 5) DECONNEXION
$router->map('GET', '/deconnexion', function(){
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->logout();
});

$router->map('POST', '/messages/[i:postId]/like', function ($postId) {
    $db = \Database\Database::getInstance();
    $likeCtrl = new \Controllers\LikeController($db);
    $likeCtrl->toggleLike($postId);
});
$router->map('POST', '/messages/[i:postId]/unlike', function ($postId) {
    $db = \Database\Database::getInstance();
    $likeCtrl = new \Controllers\LikeController($db);
    $likeCtrl->unlike($postId);
});



/** 
 * MATCHER
 */
$match = $router->match();
if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    // 404
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo 'Page introuvable.';
}
