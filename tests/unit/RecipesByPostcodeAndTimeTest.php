<?php

use App\Conditions\RecipesByPostcodeAndTime;
use PHPUnit\Framework\TestCase;

class RecipesByPostcodeAndTimeTest extends TestCase
{
    protected object $obj;

    protected function setUp(): void
    {
        $this->obj = new RecipesByPostcodeAndTime(1, '3AM', '4PM');

        parent::setUp();
    }

    /**
     * @dataProvider convertTo24FormatProvider
     */
    public function testConvertTo24Format(string $format12, int $format24)
    {
        $this->assertSame($format24, $this->obj->convertTo24Format($format12));
    }

    public function convertTo24FormatProvider(): array
    {
        return [
            ['12AM', 0],
            ['12PM', 12],
            ['1AM', 1],
            ['3PM', 15],
            ['11PM', 23],
        ];
    }

    /**
     * @dataProvider isCorrectTimeRangeProvider
     */
    public function testIsCorrectTimeRange(bool $isCorrect, string $from12, string $to12, int $from24, int $to24)
    {
        $obj = new RecipesByPostcodeAndTime(1, $from12, $to12);
        $result = $obj->isCorrectTimeRange($from24, $to24);

        if ($isCorrect) {
            $this->assertTrue($result);
        } else {
            $this->assertNotTrue($result);
        }
    }

    public function isCorrectTimeRangeProvider(): array
    {
        return [
            [true, '4PM', '8AM', 23, 8],
            [true, '11PM', '8AM', 23, 8],
            [true, '10PM', '8AM', 23, 8],
            [false, '2AM', '4PM', 1, 17],
            [true, '3AM', '3AM', 3, 16],
            [false, '6PM', '12AM', 17, 1],
            [false, '3AM', '11PM', 2, 9],
        ];
    }
}