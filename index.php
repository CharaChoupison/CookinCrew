<?php
// Au début de index.php (juste après <?php)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'vendor/autoload.php';

use Controllers\HomeController;
use Controllers\UserController;
use Database\Database;

$router = new AltoRouter();

// Chemin de base (dossier du projet), à adapter selon ta config
$router->setBasePath('/CookinCrew');

/** ROUTES **/
// Page d’accueil
$router->map('GET', '/', function () {
    $db = Database::getInstance();
    $homeController = new HomeController($db);
    $homeController->index();
}); 

// INSCRIPTION : GET (affiche formulaire), POST (traite formulaire)
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

// CONNEXION : GET (affiche formulaire), POST (traite formulaire)
$router->map('GET', '/connexion', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    // Montre la page de connexion (ex: methode showconnexionForm ou index)
    $userController->index(); // ou $userController->showconnexionForm();
});
$router->map('POST', '/connexion', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    // Traite les données du formulaire (methode connexion)
    $userController->connexion();
});

// DECONNEXION
$router->map('GET', '/deconnexion', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->logout();
});

// ADMIN (juste en GET pour l’exemple)
$router->map('GET', '/home', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->home();
});


// ADMIN (juste en GET pour l’exemple)
$router->map('GET', '/admin', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->admin();
});

/** MATCHER LA ROUTE **/
$match = $router->match();

if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    // 404
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo 'Page introuvable.';
}
