<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Handlers\Raw;

use BlueLibraries\PHP5\Dns\Handlers\Raw\RawClassTypes;
use PHPUnit\Framework\TestCase;

class RawClassTypesTest extends TestCase
{

    /**
     * @return array[]
     */
    public static function rawClassesDataProvider()
    {
        return [
            [1, 'IN'],
            [2, 'CS'],
            [3, 'CH'],
            [4, 'HS'],
            [99, null],
        ];
    }

    /**
     * @param $classId
     * @param string|null $expected
     * @dataProvider rawClassesDataProvider
     * @return void
     */
    public static function testGetClassNameByRawType($classId,  $expected)
    {
        static::assertSame($expected, RawClassTypes::getClassNameByRawType($classId));
    }

}
