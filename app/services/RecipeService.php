<?php

namespace Services;

use Models\category;
use Models\Exceptions\InternalErrorException;
use Models\Exceptions\ObjectCreationException;
use Models\recipe;
use Repositories\RecipeRepository;

class RecipeService
{
    private $recipeRepository;

    public function __construct()
    {
        $this->recipeRepository = new RecipeRepository();
    }

    /**
     * @throws ObjectCreationException
     */
    public function getAllRecipes($limit = NULL, $offset = NULL): ?array
    {
        return $this->recipeRepository->getAllRecipes($limit, $offset);
    }

    /**
     * @throws InternalErrorException
     */
    public function getRecipeByID($recipeId): ?Recipe
    {
        return $this->recipeRepository->getRecipeByID($recipeId);
    }

    /**
     * @throws InternalErrorException
     */
    public function getRecipeByCategory(category $category): ?array
    {
        return $this->recipeRepository->getRecipeByCategory($category);
    }
    public function getRecipesByUser($userId,$limit, $offset): ?array
    {
        return $this->recipeRepository->getRecipeByUser($userId, $limit, $offset);
    }

    /**
     * @throws InternalErrorException
     */
    public function createNewRecipe($recipeDetails): ?Recipe
    {
        return $this->recipeRepository->createNewRecipe($recipeDetails);
    }

    /**
     * @throws InternalErrorException
     */
    public function updateRecipe($recipeId, $recipeDetails): ?Recipe
    {
        return $this->recipeRepository->updateRecipe($recipeId, $recipeDetails);
    }

    /**
     * @throws InternalErrorException
     */
    public function deleteRecipe($recipeId): void
    {
        $this->recipeRepository->deleteRecipe($recipeId);
    }

}