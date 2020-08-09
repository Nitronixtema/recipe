<?php

use PHPUnit\Framework\TestCase;

class MostDeliveredPostcodeTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testGetResult(
        bool $isCorrect, int $suggestedPostcode, int $deliveredTimes, array $listPostcodes
    ) {
        $o = new \App\Conditions\MostDeliveredPostcode();
        foreach ($listPostcodes as $postcode) {
            $o->checkData(['postcode' => $postcode]);
        }

        $this->assertArrayHasKey('busiest_postcode', $o->getResult());

        if ($isCorrect) {
            $this->assertSame($suggestedPostcode, $o->getResult()['busiest_postcode']['postcode']);
            $this->assertSame($deliveredTimes, $o->getResult()['busiest_postcode']['delivery_count']);
        } else {
            $this->assertNotSame($suggestedPostcode, $o->getResult()['busiest_postcode']['postcode']);
            $this->assertNotSame($deliveredTimes, $o->getResult()['busiest_postcode']['delivery_count']);
        }
    }

    public function provider()
    {
        return [
            [true, 1, 4, [1, 1, 2, 3, 1, 2, 4, 5, 1]],
            [true, 111, 1, [111, 222, 333]],
            [false, 8, 2, [8, 8, 9, 9, 9]],
            [false, 8, 5, [8, 8, 9, 9, 9]],
        ];
    }
}
