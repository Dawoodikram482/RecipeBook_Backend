<?php

namespace Controllers;

use Controllers\AbstractController;
use Models\category;
use Models\Exceptions\InternalErrorException;
use Services\RecipeService;

class RecipeController extends AbstractController
{
    private $recipeService;

    public function __construct()
    {
        $this->recipeService = new RecipeService();
    }

    public function getAll()
    {
        $offset = null;
        $limit = null;
        if (isset($_GET["offset"]) && is_numeric($_GET["offset"])) {
            $offset = $_GET["offset"];
        }
        if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
            $limit = $_GET["limit"];
        }
        if (isset($_GET['category'])) {
            $category = category::createFrom($_GET['category']);
            $this->recipeService->getRecipeByCategory($category);
            return;
        }
        try {
            $recipes = $this->recipeService->getAllRecipes($limit, $offset);
        } catch (InternalErrorException $e) {
            $this->respondWithError(500, $e->getMessage());
            return;
        }
        if (empty($recipes)) {
            return $this->respondWithError(204, "No recipes found");
        }
        $this->respond($recipes);
    }

    public function getOneRecipe($id)
    {
        try {
            $recipe = $this->recipeService->getRecipeByID($id);
            if (empty($recipe)) {
                return $this->respondWithError(404, "Recipe not found");
            }
            $this->respond($recipe);
        } catch (InternalErrorException $e) {
            $this->respondWithError(500, $e->getMessage());
        }
    }

    public function createRecipe()
    {
        $token = $this->checkForJwt();
        if (empty($token)) {
            return $this->respondWithError(401, "Unauthorized");
        }
        $json = file_get_contents('php://input');
        $recipeDetails = $this->sanitize(json_decode($json));
        if (!$recipeDetails) {
            return $this->respondWithError(400, "Invalid JSON data");
        }

        // Assign user_id from token
        if (isset($token->data->id)) {
            $recipeDetails->user_id = $token->data->id;
        } else {
            return $this->respondWithError(401, "Unauthorized: Invalid user ID in token");
        }
        try {
            $recipe = $this->recipeService->createNewRecipe($recipeDetails);
            if (!empty($recipe)) {
                $this->respond($recipe);
            } else {
                $this->respondWithError(500, "Error creating recipe");
            }
        } catch (InternalErrorException $e) {
            $this->respondWithError(500, $e->getMessage());
        }
    }

    public function getRecipesByUser()
    {
        $offset = null;
        $limit = null;
        if (isset($_GET["offset"]) && is_numeric($_GET["offset"])) {
            $offset = $_GET["offset"];
        }
        if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
            $limit = $_GET["limit"];
        }
        $token = $this->checkForJwt();
        if (empty($token)) {
            return $this->respondWithError(401, "Unauthorized");
        }
        try {
            $recipes = $this->recipeService->getRecipesByUser($token->data->id, $limit, $offset);
            if (empty($recipes)) {
                return $this->respondWithError(204, "No recipes found");
            }
            $this->respond($recipes);
        } catch (InternalErrorException $e) {
            $this->respondWithError(500, $e->getMessage());
        }
    }
    public function getRecipesByCategory()
    {
        $requestBody = file_get_contents('php://input');
        $data = $this->sanitize(json_decode($requestBody, true));
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->respondWithError(400, "Invalid JSON input");
        }
        if (!isset($data['category']) || !is_string($data['category'])) {
            return $this->respondWithError(400, "Category is required and must be a string");
        }
        $category = category::createFrom($data['category']);

        try {
            $recipes = $this->recipeService->getRecipeByCategory($category);
            if (empty($recipes)) {
                return $this->respondWithError(204, "No recipes found");
            }
            $this->respond($recipes);
        } catch (InternalErrorException $e) {
            $this->respondWithError(500, $e->getMessage());
        }
    }
    public function updateRecipe($id)
    {
        $token = $this->checkForJwt();
        if (empty($token)) {
            return $this->respondWithError(401, "Unauthorized");
        }
        $requestBody = file_get_contents('php://input');
        $recipeDetails = $this->sanitize(json_decode($requestBody));
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->respondWithError(400, "Invalid JSON input");
        }
        if (!empty($recipeDetails->Image)) {
            $recipeDetails->Image = $recipeDetails->Image;
        }
        if (isset($token->data->id)) {
            $recipeDetails->user_id = $token->data->id;
        } else {
            return $this->respondWithError(401, "Unauthorized: Invalid user ID in token");
        }
        try {
            $recipe = $this->recipeService->updateRecipe($id, $recipeDetails);
            if (!empty($recipe)) {
                $this->respond($recipe);
            } else {
                $this->respondWithError(500, "Error updating recipe");
            }
        } catch (InternalErrorException $e) {
            $this->respondWithError(500, $e->getMessage());
        }
    }


    public function deleteRecipe($id)
    {
        $token = $this->checkForJwt();
        if (empty($token)) {
            return $this->respondWithError(401, "Unauthorized");
        }
        try {
            $this->recipeService->deleteRecipe($id);
            $this->respond(true);
        } catch (InternalErrorException $e) {
            $this->respondWithError(500, $e->getMessage());
        }
    }
}