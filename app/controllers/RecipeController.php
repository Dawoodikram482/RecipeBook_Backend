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
        $recipeDetails = json_decode($_POST['recipeDetails']);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->respondWithError(400, "Invalid JSON data");
        }
        if (isset($token->data->id)) {
            $recipeDetails->user_id = $token->data->id;
        } else {
            return $this->respondWithError(401, "Unauthorized: Invalid user ID in token");
        }
        if (!isset($_FILES['Image']) || $_FILES['Image']['error'] !== UPLOAD_ERR_OK) {
            return $this->respondWithError(400, "Invalid or missing image upload");
        }
        $imagePath = 'images/' . $_FILES['Image']['name'];
        if (!move_uploaded_file($_FILES['Image']['tmp_name'], $imagePath)) {
            return $this->respondWithError(500, "Failed to move uploaded file");
        }
        $imageNewName =  "recipe_book_" . uniqid();
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
        $imageUpdated = false;
        if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = 'images/' . $_FILES['Image']['name'];
            if (move_uploaded_file($_FILES['Image']['tmp_name'], $imagePath)) {
                $imageNewName = "recipe_book_" . uniqid();
                $recipeDetails->Image = $imageNewName;
                $imageUpdated = true;
            } else {
                return $this->respondWithError(500, "Failed to move uploaded file");
            }
        }

        try {
            $recipe = $this->recipeService->getRecipeById($id); // Assuming you have a method like this in recipeService
            if (!$recipe) {
                return $this->respondWithError(404, "Recipe not found");
            }
            if ($imageUpdated) {
                // Delete previous image if exists
                if (!empty($recipe->Image)) {
                    $previousImagePath = 'images/' . $recipe->Image;
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }
            } else {
                $recipeDetails->Image = $recipe->Image;
            }

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