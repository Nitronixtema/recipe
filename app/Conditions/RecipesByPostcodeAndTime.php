<?php

namespace App\Conditions;

/**
 * Count the number of deliveries to postcode * that lie within the delivery time between *
 */
class RecipesByPostcodeAndTime implements ICondition
{
    private int $count = 0;

    private string $from;

    private int $from24;

    private int $postcode;

    private int $timeRegime;

    private string $to;

    private int $to24;

    public function __construct(int $postcode, string $from, string $to)
    {
        $this->postcode = $postcode;

        $this->from = $from;
        $this->to = $to;

        $this->from24 = $this->convertTo24Format(strtoupper($from));
        $this->to24 = $this->convertTo24Format(strtoupper($to));

        $this->timeRegime = $this->from24 <=> $this->to24;
    }

    public function checkData(array $data): void
    {
        if ($this->postcode === (int)$data['postcode']) {
            $parts = explode(' ', $data['delivery']);

            if ($this->isCorrectTimeRange(
                $this->convertTo24Format($parts[1]), $this->convertTo24Format($parts[3])
            )) {
                $this->count++;
            }
        }
    }

    public function isCorrectTimeRange(int $from24, int $to24): bool
    {
        switch ($this->timeRegime) {
            case -1:
                if ($from24 > $to24) {
                    return false;
                }

                return $this->from24 <= $from24 && $this->to24 >= $to24;
            case 0:
                return true;
            case 1:
                if ($from24 > $to24) {
                    return $this->from24 <= $from24 && $this->to24 >= $to24;
                }

                return $this->from24 <= $from24 && $this->from24 <= $to24
                    || $this->to24 >= $from24 && $this->to24 >= $to24;
        }

        throw new \Exception('Wrong behavior');
    }

    public function convertTo24Format(string $time): int
    {
        $hours = (int)$time;

        $meridiem = substr($time, -2);

        if ($meridiem === 'AM') {
            if ($hours === 12) {
                return 0;
            }

            return $hours;
        }

        if ($hours === 12) {
            return 12;
        }

        return $hours + 12;
    }

    public function getResult(): array
    {
        return [
            'count_per_postcode_and_time' => [
                'postcode' => $this->postcode,
                'from' => $this->from,
                'to' => $this->to,
                'delivery_count' => $this->count,
            ]
        ];
    }
}
