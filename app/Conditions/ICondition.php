<?php

namespace App\Conditions;

interface ICondition
{
    public function checkData(array $data): void;
    public function getResult(): array;
}
