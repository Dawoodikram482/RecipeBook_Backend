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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['Image']) || $_FILES['Image']['error'] !== UPLOAD_ERR_OK) {
            return $this->respondWithError(400, "Invalid or missing image upload");
        }
        $recipeDetails = (object) $_POST;
        if (!isset($recipeDetails->RecipeTitle) || !isset($recipeDetails->Category) || !isset($recipeDetails->Ingredients) || !isset($recipeDetails->Instructions)) {
            return $this->respondWithError(400, "Missing required fields");
        }
        $recipeDetails->RecipeTitle = $this->sanitize($recipeDetails->RecipeTitle);
        $recipeDetails->Category = $this->sanitize($recipeDetails->Category);
        $recipeDetails->Ingredients = $this->sanitize($recipeDetails->Ingredients);
        $recipeDetails->Instructions = $this->sanitize($recipeDetails->Instructions);
        if (isset($token->data->id)) {
            $recipeDetails->user_id = $token->data->id;
        } else {
            return $this->respondWithError(401, "Unauthorized: Invalid user ID in token");
        }
        $imageExtension = pathinfo($_FILES['Image']['name'], PATHINFO_EXTENSION);
        $imageNewName =  "recipe_book_" . uniqid() . '.' . $imageExtension;
        $imagePath = 'images/' . $imageNewName;
        if (!move_uploaded_file($_FILES['Image']['tmp_name'], $imagePath)) {
            return $this->respondWithError(500, "Failed to move uploaded file");
        }
        $recipeDetails->Image = $imageNewName;
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
    public function getRecipesByCategory() {
        $category = $_GET['category'] ?? null; // Retrieve category from query parameters

        if (!isset($category) || !is_string($category)) {
            return $this->respondWithError(400, "Category is required and must be a string");
        }

        $categoryObject = Category::createFrom($category); // Adjust this line based on your application's logic

        try {
            $recipes = $this->recipeService->getRecipeByCategory($categoryObject);
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
        if (isset($token->data->id)) {
            $recipeDetails->user_id = $token->data->id;
        } else {
            return $this->respondWithError(401, "Unauthorized: Invalid user ID in token");
        }

        try {
            $updatedRecipe = $this->recipeService->updateRecipe($id,$recipeDetails);
            if (!empty($updatedRecipe)) {
                $this->respond($updatedRecipe);
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