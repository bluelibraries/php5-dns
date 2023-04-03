<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Handlers;

use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerFactory;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerFactoryException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\PHP5\Dns\Handlers\Types\Dig;
use BlueLibraries\PHP5\Dns\Handlers\Types\DnsGetRecord;
use BlueLibraries\PHP5\Dns\Handlers\Types\TCP;
use BlueLibraries\PHP5\Dns\Handlers\Types\UDP;
use PHPUnit\Framework\TestCase;

class DnsHandlerFactoryTest extends TestCase
{

    /**
     * @var DnsHandlerFactory $subject;
     **/
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = new DnsHandlerFactory();
    }

    /**
     * @return array[]
     */
    public static function validHandlersDataProvider()
    {
        return [
            [DnsHandlerTypes::DNS_GET_RECORD, DnsGetRecord::class],
            [DnsHandlerTypes::DIG, Dig::class],
            [DnsHandlerTypes::TCP, TCP::class],
            [DnsHandlerTypes::UDP, UDP::class],
        ];
    }

    /**
     * @param $handlerType
     * @param $expectedClass
     * @dataProvider validHandlersDataProvider
     * @return void
     * @throws DnsHandlerFactoryException
     */
    public function testCreateValidHandlers($handlerType, $expectedClass)
    {
        $this->assertSame($expectedClass, get_class($this->subject->create($handlerType)));
    }

    /**
     * @return void
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerFactoryException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Unable to build handler type: "test"
     */
    public function testCreateInvalidHandler()
    {
        $this->subject->create('test');
    }

}
