<?php

namespace App\Conditions;

/**
 * List the recipe names (alphabetically ordered) that contain in their name one of words
 */
class RecipesContainWords implements ICondition
{
    private array $checkedRecipes = [];
    private array $recipes = [];
    private array $words = [];

    public function __construct(array $words)
    {
        $this->words = $words;
    }

    public function checkData(array $data): void
    {
        $recipeName = $data['recipe'];

        if (isset($this->checkedRecipes[$recipeName])) {
            return;
        }

        $this->checkedRecipes[$recipeName] = true;

        foreach ($this->words as $word) {
            if (strpos($recipeName, $word) !== false) {
                $this->recipes[$recipeName] = true;
                return;
            }
        }
    }

    public function getResult(): array
    {
        ksort($this->recipes);

        return [
            'match_by_name' => array_keys($this->recipes)
        ];
    }
}
