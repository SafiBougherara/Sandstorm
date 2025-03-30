    <?php
session_start();
require 'vendor/autoload.php';

use Controllers\HomeController;
use Controllers\UserController;
use Controllers\CategoryController;
use Controllers\ListingController;
use Database\Database;
use Middlewares\AuthMiddleware;

// Créer une instance du routeur
$router = new AltoRouter();

// Définir le chemin de base
$router->setBasePath('/Sandstorm');

// Définir les routes
$router->map('GET', '/', function () {
    $db = Database::getInstance();
    $homeController = new HomeController($db);
    $homeController->index();
});

// Categories routes
$router->map('GET', '/categories', function() {
    $db = Database::getInstance();
    $categoryController = new CategoryController($db);
    $categoryController->index();
});

$router->map('GET', '/category/[*:slug]', function($slug) {
    $db = Database::getInstance();
    $categoryController = new CategoryController($db);
    $categoryController->view($slug);
});

// Listing routes
$router->map('GET', '/browse', function() {
    $db = Database::getInstance();
    $listingController = new ListingController($db);
    $listingController->browse();
});

$router->map('GET', '/search', function() {
    $db = Database::getInstance();
    $listingController = new ListingController($db);
    $listingController->search();
});

$router->map('GET', '/listing/[i:id]', function($id) {
    $db = Database::getInstance();
    $listingController = new ListingController($db);
    $listingController->view($id);
});

$router->map('GET', '/sell', function() {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $listingController = new ListingController($db);
    $listingController->create();
});

$router->map('POST', '/listing/store', function() {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $listingController = new ListingController($db);
    $listingController->store();
});

$router->map('GET', '/listing/[i:id]/edit', function($id) {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $listingController = new ListingController($db);
    $listingController->edit($id);
});

$router->map('POST', '/listing/[i:id]/update', function($id) {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $listingController = new ListingController($db);
    $listingController->update($id);
});

$router->map('POST', '/listing/[i:id]/delete', function($id) {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $listingController = new ListingController($db);
    $listingController->delete($id);
});

// User routes
$router->map('GET', '/register', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->register();
});

$router->map('POST', '/register', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->register(); 
});

$router->map('GET', '/login', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->login();
});

$router->map('GET', '/logout', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->logout();
});

$router->map('POST', '/login', function () {
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->login();
});

$router->map('GET', '/admin', function () {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->admin();
});

$router->map('GET', '/dashboard', function() {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->dashboard();
});

$router->map('GET|POST', '/profile', function() {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->profile();
});

$router->map('GET', '/my-listings', function() {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $userController = new UserController($db);
    $userController->myListings();
});

// Matcher et gérer la requête
$match = $router->match();

// Call closure or throw 404 status
if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    // No route was matched
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo '404 Page Not Found';
}
