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
        $recipeDetails = $this->sanitize($_POST['recipeDetails']);
        $recipeDetails->userId = $token->data->Id;
        $recipeDetails->Image = $_FILES['image'];
        $recipeDetails->Category = category::createFrom($recipeDetails->Category);
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
            $recipes = $this->recipeService->getRecipesByUser($token->data->Id, $limit, $offset);
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

        $category = category::createFrom($_GET['category']);
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
        $recipeDetails = $this->sanitize($_POST['recipeDetails']);
        $recipeDetails->userId = $token->data->Id;
        $recipeDetails->Image = $_FILES['image'];
        $recipeDetails->Category = category::createFrom($recipeDetails->Category);
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