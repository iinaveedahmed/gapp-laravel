<?php

namespace Ipaas\Gapp\Tests\Helpers;

use Ipaas\Gapp\Tests\TestCase;

class ConverterTest extends TestCase
{
    /**
     * @test
     */
    public function itTestsNormalizedName()
    {
        $cases = [
            'te sting' => 'te  sting',
            'test ing' => 'test      ing',
            'tes ting' => "tes\nting",
            't es tin g' => "t     es\ntin  g",
        ];

        foreach ($cases as $expected => $case) {
            $result = normalizedName($case);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @test
     */
    public function itTestsBoolifyList()
    {
        $cases = ['true', 'false', 'TRUE', 'FALSE', true, false, TRUE, FALSE, 0, 1, '0', '1', '', ' test'];

        foreach ($cases as $key => $case) {
            boolifyList($cases, $key);
        }

        $this->assertEquals(
            [true, false, true, false, true, false, TRUE, FALSE, false, true, false, true, false, true],
            $cases
        );
    }
}
