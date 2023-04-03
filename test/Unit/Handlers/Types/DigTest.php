<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Handlers\Types;

use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\Types\Dig;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use PHPUnit\Framework\TestCase;

class DigTest extends TestCase
{
    /**
     * @var Dig
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(Dig::class)
            ->setMethods(['executeCommand'])
            ->getMock();
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionMessage Invalid hostname, it must not be empty!
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::HOSTNAME_EMPTY
     */
    public function testGetDnsDataEmptyHostName()
    {
        $this->subject->getDnsData('', RecordTypes::ALL);
    }

    /**
     * @return void
     * @throws DnsHandlerException
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
     * @throws DnsHandlerException
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
     * @throws DnsHandlerException
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
     * @throws DnsHandlerException
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
     * @throws DnsHandlerException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionMessage Invalid records typeId: -2 host "test.com" !
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::TYPE_ID_INVALID
     */
    public function testGetDnsDataInvalidTypeId()
    {
        $this->subject->getDnsData('test.com', -2);
    }

    /**
     * @throws DnsHandlerException
     */
    public function testGetDnsDataEmptyData()
    {
        $this->setValueInExecuteCommand([]);
        $this->assertSame([], $this->subject->getDnsData('test.com', RecordTypes::ALL));
    }

    /**
     * @throws DnsHandlerException
     */
    public function testGetDnsDataValidData()
    {
        $this->setValueInExecuteCommand(['test.com 0 IN A 20.81.111.85']);
        $this->assertSame(
            [
                [
                    'host'  => 'test.com',
                    'ttl'   => 0,
                    'class' => 'IN',
                    'type'  => 'A',
                    'ip'    => '20.81.111.85',
                ]
            ],
            $this->subject->getDnsData('test.com', RecordTypes::ALL)
        );
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

    protected function setValueInExecuteCommand(array $value)
    {
        $this->subject->method('executeCommand')
            ->willReturn($value);
    }

    public function testGetDnsDataNotFoundResultMakesOnlyOneCall()
    {
        $this->setValueInExecuteCommand([]);
        $this->subject->expects(
            $this->once()
        )->method('executeCommand');
        $this->assertSame([], $this->subject->getDnsData('test.com', RecordTypes::TXT));
    }

    public function testExecuteCommandInvalidArgumentsThrowsError()
    {
        $subject = new Dig();
        $this->assertSame([], $subject->getDnsRawResult('ls', RecordTypes::TXT));
    }

    public function testGetDnsRawResultInvalidGetCommandReturnsEmptyArray()
    {
        $subject = $this->getMockBuilder(Dig::class)
            ->setMethods(['getCommand'])
            ->getMock();
        $subject->method('getCommand')->willReturn('ls');
        $this->assertSame([], $subject->getDnsRawResult('test.com', RecordTypes::TXT));
    }

    public function testGetCommandNoRecordName()
    {
        $this->assertSame([], $this->subject->getDnsRawResult('test.com', 99999999999999));
    }

    /**
     * @throws DnsHandlerException
     */
    public function testCanExecuteDigType()
    {
        $subject = new Dig();
        $this->assertTrue(is_bool($subject->canUseIt()));
    }

    /**
     * @return array[]
     */
    public static function validCommandsDataProvider()
    {
        return [
            ['', false],
            ['ls', false],
            ['dir', false],
            ['dig | ls', false],
            ['wget', false],
            ['wget', false],
            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com ABCDEFGHIJKLM', false],
            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com A ', false],
            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com A @8.8', false],
            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com A @8.8.8.8 ', false],
            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com A @192.168.0.1 ', false],
            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com A @192.1168.0.1', false],

            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com ABCDE', true],
            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com ABC123-DE', true],
            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com A', true],
            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com A @8.8.8.8', true],
            ['dig +nocmd +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com A @192.168.0.1', true],
            ['dig +nocmd +bufsize=1024 +noall +noauthority +answer +nomultiline +tries=1 +time=5 test.com A @192.168.0.1', true],
        ];
    }

    /**
     * @return void
     * @dataProvider validCommandsDataProvider
     */
    public function testIsValidCommand($command, $expected)
    {
        $this->assertSame($expected, $this->subject->isValidCommand($command));
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::INVALID_NAMESERVER
     * @expectedExceptionMessage Unable to set nameserver, as "test" is an invalid IPV4 format!
     */
    public function testSetNameserverInvalidThrowsException()
    {
        $this->subject->setNameserver('test');
    }

    /**
     * @throws DnsHandlerException
     */
    public function testSetNameserverValidReturnsSelf()
    {
        $this->assertSame($this->subject, $this->subject->setNameserver('8.8.8.8'));
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::ERR_UNABLE_TO_GET_RECORD
     */
    public function testInvalidOutput()
    {
        $subject = $this->getMockBuilder(Dig::class)
            ->setMethods(['isValidOutput', 'getCommand', 'isValidCommand'])
            ->getMock();

        $subject->method('isValidOutput')
            ->willReturn(false);
        $subject->method('getCommand')
            ->willReturn('test');
        $subject->method('isValidCommand')
            ->willReturn(true);

        $subject->getDnsRawResult('test.com', RecordTypes::A);
    }

}
