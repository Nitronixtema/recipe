<?php

namespace App;

use App\Conditions\ICondition;

class Processor
{
	private const ERROR_LENGTH = 45;

	/**
	 * @var ICondition[]
	 */
	protected array $conditions = [];

	public function addCondition(ICondition $condition): void
	{
		$this->conditions[] = $condition;
	}

	public function getResult(): array
    {
        $result = [];

        foreach ($this->conditions as $condition) {
            $result += $condition->getResult();
        }

        return $result;
    }

	public function processData(array $data): void
	{
		if (!$this->isValidData($data)) {
			throw new \Exception(
			    'Json file is invalid: '
                . substr(var_export($data, true), 0, self::ERROR_LENGTH)
            );
		}

		foreach ($this->conditions as $condition) {
			$condition->checkData($data);
		}
	}

	/**
	 * We may check each dataset
	 */
	public function isValidData(array $data): bool
	{
		return isset($data['postcode'], $data['recipe'], $data['delivery']);
	}
}
