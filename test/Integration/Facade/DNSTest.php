<?php

namespace BlueLibraries\PHP5\Dns\Test\Integration\Facade;

use BlueLibraries\PHP5\Dns\Facade\DNS;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerFactoryException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\PHP5\Dns\Records\RecordException;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use PHPUnit\Framework\TestCase;

class DNSTest extends TestCase
{

    /**
     * @return array[]
     */
    public static function getRecordsDataProvider()
    {
        return [
            ['', [], []],
            ['test.com', RecordTypes::TXT],
            ['google.com', [RecordTypes::A]],
            ['test.com', [RecordTypes::NS]],
        ];
    }

    /**
     * @param $host
     * @param int|int[] $types
     * @return void
     * @throws DnsHandlerException
     * @throws DnsHandlerFactoryException
     * @throws RecordException
     * @dataProvider getRecordsDataProvider
     */
    public function testGetRecords($host, $types)
    {
        static::assertTrue(is_array(DNS::getRecords($host, $types, DnsHandlerTypes::TCP, true, '8.8.8.8')));
    }

}
