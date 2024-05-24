<?php

namespace Repositories;

use Cassandra\Date;
use DateTime;
use Exception;
use Models\category;
use Models\Exceptions\InternalErrorException;
use Models\Exceptions\ObjectCreationException;
use Models\recipe;
use Repositories\AbstractRepository;
use Services\UserService;

class RecipeRepository extends AbstractRepository
{
    private $userService;

    /**
     * @throws InternalErrorException
     */
    public function __construct()
    {
        parent::__construct();
        $this->userService = new userService();
    }

    /**
     * @throws ObjectCreationException
     * @throws InternalErrorException
     */
    public function getAllRecipes($limit, $offset): ?array
    {
        $query = "SELECT RecipeId, RecipeTitle, Ingredients, Instructions, Image, Category,user_id,Createdate FROM Recipe";
        $params = array();
        if (!empty($this->constructPaginationClause($limit, $offset))) {
            $query .= $this->constructPaginationClause($limit, $offset);
            $params = array(
                ":limit" => $limit,
                ":offset" => $offset
            );
        }
        $results = $this->ExecQueryAndGetResults($query, $params);
        if (!empty($results)) {
            $recipes = array();
            foreach ($results as $recipe) {
                $recipes[] = $this->makeRecipe($recipe);
            }
            return $recipes;
        }

        return null;

    }

    /**
     * @throws ObjectCreationException
     * @throws InternalErrorException
     */
    public function getRecipeById($recipeId): ?recipe
    {
        $query = "SELECT RecipeId, RecipeTitle, Ingredients, Instructions, Image, Category,Createdate,user_id FROM Recipe WHERE RecipeId = :recipeId";
        $params = [":recipeId" => $recipeId];
        $result = $this->ExecQueryAndGetResults($query, $params, false);
        if (!empty($result)) {
            return $this->makeRecipe($result);
        }
        return null;
    }

    /**
     * @throws ObjectCreationException
     * @throws InternalErrorException
     */
    public function getRecipeByCategory(category $category): ?array
    {
        $query = "SELECT RecipeId, RecipeTitle, Ingredients, Instructions, Image, Category,user_id,Createdate FROM Recipe WHERE Category = :category";
        $params = [":category" => category::getCategoryType($category)];
        $results = $this->ExecQueryAndGetResults($query, $params);
        if (!empty($results)) {
            $recipes = array();
            foreach ($results as $recipe) {
                $recipes[] = $this->makeRecipe($recipe);
            }
            return $recipes;
        }
        return null;
    }

    public function getRecipeByUser($userId, $limit = null, $offset = null): ?array
    {
        $query = "SELECT RecipeId, RecipeTitle, Ingredients, Instructions, Image, Category, user_id, Createdate FROM Recipe WHERE user_id = :userId";
        $parameters = [":userId" => $userId];

        if (!empty($this->constructPaginationClause($limit, $offset))) {
            $query .= " LIMIT :limit OFFSET :offset ";
            $parameters[] = [":limit" => $limit, ":offset" => $offset];
        }

        $results = $this->ExecQueryAndGetResults($query, $parameters);
        if (!empty($results)) {
            $recipes = array();
            foreach ($results as $recipe) {
                $recipes[] = $this->makeRecipe($recipe);
            }
            return $recipes;
        }
        return null;
    }

    /**
     * @throws InternalErrorException
     * @throws ObjectCreationException
     */
    public function createNewRecipe($recipe): ?recipe
    {
        $query = "INSERT INTO Recipe (RecipeTitle, Ingredients, Instructions, Image, Category, user_id) VALUES (:RecipeTitle, :Ingredients, :Instructions, :Image, :Category, :user_id)";
        $params = [
            ":RecipeTitle" => $recipe->RecipeTitle,
            ":Ingredients" => $recipe->Ingredients,
            ":Instructions" => $recipe->Instructions,
            ":Image" => $recipe->Image,
            ":Category" => category::getCategoryType($recipe->Category), // get the category type from the category enum (category.php
            ":user_id" => $recipe->user_id,
        ];
        $result = $this->ExecQueryAndGetResults($query, $params, false, true);
        if (is_numeric($result)) {
            return $this->getRecipeById($result);
        } else {
            throw new InternalErrorException("Something went wrong while retrieving an inserted Recipe");
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function deleteRecipe($recipeId): bool
    {
        $query = "DELETE FROM Recipe WHERE RecipeId = :recipeId";
        $params = [":recipeId" => $recipeId];
        $result = $this->ExecQueryAndGetResults($query, $params);
        return is_bool($result) ? $result : throw  new InternalErrorException("Something went wrong  in App while deleting recipe");
    }

    /**
     * @throws InternalErrorException
     * @throws ObjectCreationException
     */
    public function updateRecipe($recipe, $recipeId): ?recipe
    {
        $query = "UPDATE Recipe SET RecipeTitle = :RecipeTitle, Ingredients = :Ingredients, Instructions = :Instructions, Image = :Image, Category = :Category, user_id = :user_id WHERE RecipeId = :RecipeId";
        $params = [
            ":RecipeTitle" => $recipe->RecipeTitle,
            ":Ingredients" => $recipe->Ingredients,
            ":Instructions" => $recipe->Instructions,
            ":Image" => $recipe->Image,
            ":Category" => category::getCategoryType($recipe->Category),
            ":user_id" => $recipe->user_id,
            ":RecipeId" => $recipeId
        ];
        $result = $this->ExecQueryAndGetResults($query, $params);
        return is_bool($result) ? $this->getRecipeById($recipeId) : throw  new InternalErrorException("Something went wrong  in App while updating recipe");
    }

    /**
     * @throws ObjectCreationException
     */
    public function makeRecipe($recipeRow): recipe
    {
        try {
            $recipe = new recipe();
            $recipe->setRecipeId($recipeRow['RecipeId']);
            $recipe->setRecipeTitle($recipeRow['RecipeTitle']);
            $recipe->setIngredients($recipeRow['Ingredients']);
            $recipe->setInstructions($recipeRow['Instructions']);
            $recipe->setImage($recipeRow['Image']);
            $recipe->setUser($this->userService->getUserById($recipeRow['user_id']));
            $recipe->setCategory(Category::createFrom($recipeRow['Category']));
            $recipe->setCreatedate(new DateTime($recipeRow['Createdate']));
            return $recipe;
        } catch (Exception $e) {
            throw new ObjectCreationException("Error creating recipe object");
        }
    }
}