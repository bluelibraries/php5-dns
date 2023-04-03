<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit;

use BlueLibraries\PHP5\Dns\DnsRecords;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerInterface;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use BlueLibraries\PHP5\Dns\Records\RecordException;
use BlueLibraries\PHP5\Dns\Records\RecordFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DnsRecordsTest extends TestCase
{
    /** 
  @var DnsRecords $subject; 
**/ 
 private $subject;

    /**
     * @var DnsHandlerInterface|MockObject
     */
    private $handler;

    /**
     * @var RecordFactory|MockObject
     */
    private $factory;


    public function setUp()
    {
        parent::setUp();

        $this->handler = $this->getMockBuilder(DnsHandlerInterface::class)
            ->getMock();
        $this->factory = $this->getMockBuilder(RecordFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject = new DnsRecords($this->handler, $this->factory);
    }

    /**
     * @throws RecordException
     * @throws DnsHandlerException
     */
    public function testGetRecordsEmptyArray()
    {
        $this->handler->method('getDnsData')->willReturn([]);
        $this->factory->expects($this->never())->method('create');
        $this->assertSame([], $this->subject->get('test.test', RecordTypes::A));
    }

}
