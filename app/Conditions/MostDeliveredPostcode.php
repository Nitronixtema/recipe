<?php

namespace App\Conditions;

/**
 * Find the postcode with most delivered recipes.
 */
class MostDeliveredPostcode implements ICondition
{
    private array $countPerItem = [];

    public function checkData(array $data): void
    {
        $this->countPerItem[$data['postcode']] = ($this->countPerItem[$data['postcode']] ?? 0) + 1;
    }

    public function getResult(): array
    {
        arsort($this->countPerItem);

        return [
            'busiest_postcode' => [
                'postcode' => key($this->countPerItem),
                'delivery_count' => current($this->countPerItem),
            ]
        ];
    }
}
