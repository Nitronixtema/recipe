<?php

namespace App\Conditions;

/**
 * Count the number of unique recipe names.
 * Count the number of occurences for each unique recipe name
 *   (alphabetically ordered by recipe name).
 */
class UniqueRecipes implements ICondition
{
    private array $countPerRecipe = [];

    public function checkData(array $data): void
    {
        $this->countPerRecipe[$data['recipe']] = ($this->countPerRecipe[$data['recipe']] ?? 0) + 1;
    }

    public function getResult(): array
    {
        $countPerRecipe = [];

        ksort($this->countPerRecipe);

        foreach ($this->countPerRecipe as $recipeName => $count) {
            $countPerRecipe[] = [
                'recipe' => $recipeName,
                'count' => $count,
            ];
        }

        return [
            'unique_recipe_count' => count($this->countPerRecipe),
            'count_per_recipe' => $countPerRecipe,
        ];
    }
}
