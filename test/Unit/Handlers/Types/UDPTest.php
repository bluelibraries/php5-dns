<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Handlers\Types;

use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\Raw\RawDataException;
use BlueLibraries\PHP5\Dns\Handlers\Types\UDP;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UDPTest extends TestCase
{

    /** 
  @var UDP $subject; 
**/ 
 protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = new UDP();
    }

    public function testSetPort()
    {
        $this->assertSame($this->subject, $this->subject->setPort(54));
    }

    public function testGtPort()
    {
        $this->assertSame(53, $this->subject->getPort());
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @throws RawDataException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::ERR_UNABLE_TO_WRITE_TO_UDP_SOCKET
     * @expectedExceptionMessage Failed to write question to UDP socket
     */
    public function testUnableTOWriteQuestionLengthToSocket()
    {
        /**
         * @var UDP|MockObject $subject
         */
        $subject = $this->getMockBuilder(UDP::class)
            ->setMethods(['read', 'write', 'close', 'getSocket'])
            ->getMock();

        $subject->method('getSocket')
            ->willReturn(false);

        $subject->method('write')
            ->willReturn(null);

        $subject->getDnsData('bluelibraries.com', RecordTypes::TXT);
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @throws RawDataException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::ERR_UNABLE_TO_READ_DATA_BUFFER
     * @expectedExceptionMessage Failed to read data buffer
     */
    public function testUnableTOReadSizeFromSocket()
    {
        /**
         * @var UDP|MockObject $subject
         */
        $subject = $this->getMockBuilder(UDP::class)
            ->setMethods(['read', 'write', 'close', 'getSocket'])
            ->getMock();

        $subject->method('write')
            ->willReturn(1);
        $subject->method('getSocket')
            ->willReturn(false);

        $subject->getDnsData('bluelibraries.com', RecordTypes::TXT);
    }

    public function testGetDnsDataNull()
    {
        /**
         * @var UDP|MockObject $subject
         */
        $subject = $this->getMockBuilder(UDP::class)
            ->setMethods(['read', 'write', 'close', 'query'])
            ->getMock();

        $subject->method('query')
            ->willReturn(null);

        $subject->setRetries(-1);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

    public function testQuerySocketNull() {
        /**
         * @var UDP|MockObject $subject
         */
        $subject = $this->getMockBuilder(UDP::class)
            ->setMethods(['getSocket'])
            ->getMock();

        $subject->method('getSocket')
            ->willReturn(null);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

}
