<?php

namespace Models;

use DateTime;

class recipe implements \JsonSerializable
{
    private int $RecipeId;
    private string $RecipeTitle;
    private string $Ingredients;
    private string $Instructions;
    public string $Image;
    private user $User;
    private category $Category;
    private DateTime $Createdate;
    public function __construct()
    {
    }
    /**
     * @return int
     */
    public function getRecipeId(): int
    {
        return $this->RecipeId;
    }
    public function setRecipeId(int $RecipeId): void
    {
        $this->RecipeId = $RecipeId;
    }
    public function getRecipeTitle(): string
    {
        return $this->RecipeTitle;
    }
    public function setRecipeTitle(string $RecipeTitle): void
    {
        $this->RecipeTitle = $RecipeTitle;
    }
    public function getIngredients(): string
    {
        return $this->Ingredients;
    }
    public function setIngredients(string $Ingredients): void
    {
        $this->Ingredients = $Ingredients;
    }
    public function getInstructions(): string
    {
        return $this->Instructions;
    }
    public function setInstructions(string $Instructions): void
    {
        $this->Instructions = $Instructions;
    }
    public function getImage(): string
    {
        return $this->Image;
    }
    public function setImage(string $Image): void
    {
        $this->Image = $Image;
    }
    public function getUser(): user
    {
        return $this->User;
    }
    public function setUser(user $User): void
    {
        $this->User = $User;
    }
    public function getCategory(): category
    {
        return $this->Category;
    }
    public function setCategory(category $Category): void
    {
        $this->Category = $Category;
    }
    public function getCreatedate(): DateTime
    {
        return $this->Createdate;
    }
    public function setCreatedate(DateTime $Createdate): void
    {
        $this->Createdate = $Createdate;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}