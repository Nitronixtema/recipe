<?php

use PHPUnit\Framework\TestCase;

class UniqueRecipesTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testGetResult(bool $isCorrect, int $uniqRecipesCount, array $names, array $counts)
    {
        $o = new \App\Conditions\UniqueRecipes();
        foreach ($names as $name) {
            $o->checkData(['recipe' => $name]);
        }

        $result = $o->getResult();

        $this->assertArrayHasKey('unique_recipe_count', $result);
        $this->assertArrayHasKey('count_per_recipe', $result);

        if ($isCorrect) {
            $this->assertSame($uniqRecipesCount, $result['unique_recipe_count']);

            $i = 0;
            foreach ($counts as $name => $count) {
                $this->assertSame($result['count_per_recipe'][$i]['recipe'], $name);
                $this->assertSame($result['count_per_recipe'][$i++]['count'], $count);
            }
        } else {
            $this->assertNotSame($uniqRecipesCount, $result['unique_recipe_count']);
        }
    }

    public function provider()
    {
        return [
            [true, 2, ['exp', '32434f', 'exp'], ['32434f' => 1, 'exp' => 2, ]],
            [true, 1, ['kjdfsd another expression'], ['kjdfsd another expression' => 1]],
            [true, 3, ['exp', 'exp', 'exp', 'exp0', ''], ['' => 1, 'exp' => 3, 'exp0' => 1]],
            [false, 8, ['33'], ['33' => 11]],
        ];
    }
}
