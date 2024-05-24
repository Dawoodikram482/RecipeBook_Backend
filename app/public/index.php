<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

error_reporting(E_ALL);
ini_set("display_errors", 1);

require __DIR__ . '/../vendor/autoload.php';

// Create Router instance
$router = new \Bramus\Router\Router();

$router->setNamespace('Controllers');

$base = '/api';
// User Management endpoints
$router->post($base.'/users/login', 'UserController@login');
$router->get($base.'/users', 'UserController@getAll');

$router->get($base.'/recipes', 'RecipeController@getAll');
$router->get($base.'/recipes/(\d+)', 'RecipeController@getOneRecipe');
$router->post($base.'/recipes', 'RecipeController@createRecipe');
$router->get($base.'/recipes/user', 'RecipeController@getRecipesByUser');
$router->get($base.'/recipes/category', 'RecipeController@getRecipesByCategory');
$router->put($base.'/recipes/(\d+)', 'RecipeController@updateRecipe');
$router->delete($base.'/recipes/(\d+)', 'RecipeController@deleteRecipe');
$router->run();
?>