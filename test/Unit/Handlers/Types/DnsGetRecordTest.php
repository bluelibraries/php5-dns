<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Handlers\Types;

use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\Types\DnsGetRecord;
use BlueLibraries\PHP5\Dns\Records\RecordException;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DnsGetRecordTest extends TestCase
{
    /**
     * @var DnsGetRecord|MockObject
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->getMockBuilder(DnsGetRecord::class)
            ->setMethods(['getDnsRecord'])
            ->getMock();
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::HOSTNAME_EMPTY
     * @expectedExceptionMessage Invalid hostname, it must not be empty!
     */
    public function testGetDnsDataEmptyHostName()
    {
        $this->subject->getDnsData('', RecordTypes::ALL);
    }

    /**
     * @return void
     * @throws RecordException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionMessage Invalid hostname "fo" length. It must be 3 or more!
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::HOSTNAME_LENGTH_TOO_SMALL
     */
    public function testGetDnsDataInvalidHostNameLength()
    {
        $this->subject->getDnsData('fo', RecordTypes::ALL);
    }

    /**
     * @return void
     * @throws RecordException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionMessage Invalid hostname "ana*are*mere.com" format! (characters "A-Za-z0-9.-", max length 63 chars allowed)
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::HOSTNAME_FORMAT_INVALID
     */
    public function testGetDnsDataInvalidHostNameBadCharacters()
    {
        $this->subject->getDnsData('ana*are*mere.com', RecordTypes::ALL);
    }

    /**
     * @return void
     * @throws RecordException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionMessage Invalid hostname "an\t\naaremere.com" format! (characters "A-Za-z0-9.-", max length 63 chars allowed)
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::HOSTNAME_FORMAT_INVALID
     */
    public function testGetDnsDataInvalidHostNameBadSpecialCharacters()
    {
        $this->subject->getDnsData("an\t\naaremere.com", RecordTypes::ALL);
    }

    /**
     * @return void
     * @throws RecordException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::HOSTNAME_LENGTH_INVALID
     */
    public function testGetDnsDataHostNameFormatExceededLength()
    {
        $hostName = str_repeat('a', 250) . '.com';
        $this->subject->getDnsData($hostName, RecordTypes::ALL);
    }

    /**
     * @return void
     * @throws RecordException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::HOSTNAME_FORMAT_INVALID
     */
    public function testGetDnsDataHostTLDExtensionFormatExceededLength()
    {
        $hostName = 'a.' . str_repeat('b', 64);
        $this->subject->getDnsData($hostName, RecordTypes::ALL);
    }

    /**
     * @return void
     * @throws RecordException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionMessage Invalid records typeId: 0 host "test.com" !
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::TYPE_ID_INVALID
     */
    public function testGetDnsDataInvalidTypeId()
    {
        $this->subject->getDnsData('test.com', 0);
    }

    /**
     * @throws DnsHandlerException
     */
    public function testGetDnsDataEmptyData()
    {
        $this->setValueInGetDnsRecord([]);
        $this->assertSame([], $this->subject->getDnsData('test.com', RecordTypes::ALL));
    }

    /**
     * @throws DnsHandlerException
     */
    public function testGetDnsDataValidData()
    {
        $value = [
            [
                'host'  => 'test.com',
                'class' => 'IN',
                'ttl'   => 0,
                'type'  => 'A',
                'ip'    => '20.81.111.85',
            ]
        ];
        $this->setValueInGetDnsRecord($value);
        $this->assertSame($value, $this->subject->getDnsData('test.com', RecordTypes::ALL));
    }

    public function testGetTimeout()
    {
        $this->assertSame(5, $this->subject->getTimeout());
    }

    public function testSetTimeout()
    {
        $this->subject->setTimeout(100);
        $this->assertSame(100, $this->subject->getTimeout());
    }

    public function testSetTimeoutSameObject()
    {
        $this->assertSame($this->subject, $this->subject->setTimeout(4));
    }

    public function testGetRetries()
    {
        $this->assertSame(2, $this->subject->getRetries());
    }

    public function testSetRetries()
    {
        $this->subject->setRetries(9);
        $this->assertSame(9, $this->subject->getRetries());
    }

    public function testSetRetriesSameObject()
    {
        $this->assertSame($this->subject, $this->subject->setRetries(3));
    }

    protected function setValueInGetDnsRecord($value)
    {
        $this->subject->method('getDnsRecord')
            ->willReturn($value);
    }

    public function testGetDnsRawNotFoundResultValue()
    {
        $this->setValueInGetDnsRecord(false);
        $this->assertSame([], $this->subject->getDnsRawResult('test.com', RecordTypes::TXT));
    }

    public function testGetDnsRawNotFoundResultMakesOnlyOneCall()
    {
        $this->setValueInGetDnsRecord(false);
        $this->subject->expects(
            $this->once()
        )->method('getDnsRecord');
        $this->assertSame([], $this->subject->getDnsRawResult('test.com', RecordTypes::TXT));
    }

    public function testGetDnsRawEmptyResultMaxRetries()
    {
        $this->setValueInGetDnsRecord([]);
        $this->subject->expects(
            $this->exactly($this->subject->getRetries() + 1)
        )->method('getDnsRecord');
        $this->assertSame([], $this->subject->getDnsRawResult('test.com', RecordTypes::TXT));
    }

    public function testGetDnsRawResult()
    {
        $value = [
            [
                'host'  => 'test.com',
                'class' => 'IN',
                'ttl'   => 0,
                'type'  => 'A',
                'ip'    => '20.81.111.85',
            ]
        ];
        $this->setValueInGetDnsRecord($value);
        $this->assertSame($value, $this->subject->getDnsRawResult('test.com', RecordTypes::TXT));
    }

    public function testGetDnsRecordInvalidValueExpectsError()
    {
        $subject = new DnsGetRecord();
        $this->assertSame([], $subject->getDnsRawResult('', RecordTypes::TXT));
    }

    /**
     * @return void
     * @throws RecordException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionMessage Unable to set nameserver, as `dns_get_record` cannot use custom nameservers!
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::UNABLE_TO_USE_CUSTOM_NAMESERVER
     */
    public function testSetNameserverNullValueThrowsException()
    {
        $this->subject->setNameserver(null);
    }

    /**
     * @return void
     * @throws RecordException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionMessage Unable to set nameserver, as `dns_get_record` cannot use custom nameservers!
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::UNABLE_TO_USE_CUSTOM_NAMESERVER
     */
    public function testSetNameserverStringValueThrowsException()
    {
        $this->subject->setNameserver('8.8.8.8');
    }

    public function testGetUpdatedRecordsData()
    {
        $this->assertSame(
            [
                ['host' => 'test.com', 'class' => 'IN', 'type' => 'TXT', 'txt' => 'text test'],
                ['host' => 'test.com', 'class' => 'IN', 'type' => 'NAPTR', 'order' => 2, 'flag' => 3],
            ],
            $this->subject->getUpdatedRecordsData(
                [
                    ['host' => 'test.com', 'class' => 'IN', 'type' => 'TXT', 'txt' => 'text test'],
                    ['host' => 'test.com', 'class' => 'IN', 'type' => 'NAPTR', 'order' => 2, 'flags' => 3],
                ]
            ));
    }

}
