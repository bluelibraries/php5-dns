<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Handlers\Types;

use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\Raw\RawDataException;
use BlueLibraries\PHP5\Dns\Handlers\Types\TCP;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TCPTest extends TestCase
{
    /**
     * @var TCP
     */
    /** 
  @var TCP $subject; 
**/ 
 protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = new TCP();
    }

    public function testSetPort()
    {
        $this->assertSame($this->subject, $this->subject->setPort(54));
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @throws RawDataException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::ERR_UNABLE_TO_WRITE_QUESTION_LENGTH_TO_TCP_SOCKET
     * @expectedExceptionMessage Failed to write question length to TCP socket
     */
    public function testUnableTOWriteQuestionLengthToSocket()
    {
        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->setMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('write')
            ->willReturn(null);

        $subject->getDnsData('bluelibraries.com', RecordTypes::TXT);
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @throws RawDataException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::ERR_UNABLE_TO_READ_SIZE_FROM_TCP_SOCKET
     * @expectedExceptionMessage Failed to read size from TCP socket
     */
    public function testUnableTOReadSizeFromSocket()
    {
        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->setMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('write')
            ->willReturn(1);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @throws RawDataException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::ERR_UNABLE_TO_WRITE_QUESTION_TO_TCP_SOCKET
     * @expectedExceptionMessage Failed to write question to TCP socket
     */
    public function testUnableToWriteQuestionToSocketNoRetries()
    {
        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->setMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('read')
            ->willReturn('test');

        $subject->method('write')
            ->willReturnOnConsecutiveCalls(1, null);

        $subject->setRetries(-1);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @throws RawDataException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::ERR_UNABLE_TO_WRITE_QUESTION_LENGTH_TO_TCP_SOCKET
     * @expectedExceptionMessage Failed to write question length to TCP socket
     */
    public function testUnableTOWriteSocketWithRetries()
    {
        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->setMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('read')
            ->willReturnOnConsecutiveCalls('test', null);

        $subject->method('write')
            ->willReturnOnConsecutiveCalls(2, 2);

        $subject->setRetries(1);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

    public function testUnableTOWriteSocketWithValidAnswerAfterRetry()
    {
        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->setMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('read')
            ->willReturnOnConsecutiveCalls(chr(0) . chr(42),
                base64_decode('hnKBgAABAAEAAAAABGFzdXMDY29tAAABAAHADAABAAEAADDvAARnCgTY'
                )
            );

        $subject->method('write')
            ->willReturnOnConsecutiveCalls(0, 2, 2, 2, 0, 2);

        $subject->setRetries(2);

        $this->assertSame([
            [
                'host'  => 'asus.com',
                'ttl'   => 12527,
                'class' => 'IN',
                'type'  => 'A',
                'ip'    => '103.10.4.216',
            ]
        ],
            $subject->getDnsData('bluelibraries.com', RecordTypes::TXT)
        );
    }


    public function testGetDnsDataNull()
    {
        /**
         * @var TCP|MockObject $subject
         */
        $subject = $this->getMockBuilder(TCP::class)
            ->setMethods(['read', 'write', 'close'])
            ->getMock();

        $subject->method('read')
            ->willReturnOnConsecutiveCalls('test', null);

        $subject->method('write')
            ->willReturnOnConsecutiveCalls(2, 2);

        $subject->setRetries(-1);

        $this->assertSame([], $subject->getDnsData('bluelibraries.com', RecordTypes::TXT));
    }

}
