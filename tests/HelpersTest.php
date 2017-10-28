<?php

use PHPUnit\Framework\TestCase;
use function Utils\Helpers\{
    isRealString,
    arrayFind
};

/**
 * @covers \Utils\Helpers
 */
class HelpersTest extends TestCase {

    public function stringProvider()
    {
        return [
            'Space in between' => ['Some User', TRUE],
            'Leading space'    => ['  user', TRUE],
            'Trailing space'   => ['something   ', TRUE],
            'Empty string'     => ['', FALSE],
            'Space only'       => ['  ', FALSE],
            'NULL value'       => [NULL, FALSE]
        ];
    }

    /**
     * @dataProvider stringProvider
     */
    public function testRealString($str, $expected)
    {
        $this->assertEquals(
            $expected,
            isRealString($str)
        );
    }

    public function testArrayFindReturnFirst()
    {
        $arr = [1,2,3,45,6];
        $callback = function($item){
            return $item > 1;
        };

        $item = arrayFind($arr, $callback);

        $this->assertEquals(2, $item);
    }

    public function testArrayFindReturnNull()
    {
        $arr = [1,2,3,45,6];
        $callback = function($item){
            return $item === '45';
        };

        $item = arrayFind($arr, $callback);

        $this->assertNull($item);
    }
}
