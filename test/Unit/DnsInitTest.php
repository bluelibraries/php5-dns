<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit;

use BlueLibraries\PHP5\Dns\DnsRecords;
use BlueLibraries\PHP5\Dns\Handlers\Types\DnsGetRecord;
use BlueLibraries\PHP5\Dns\Handlers\Types\TCP;
use BlueLibraries\PHP5\Dns\Records\RecordFactory;
use PHPUnit\Framework\TestCase;

class DnsInitTest extends TestCase
{

    public function testInjectNullHandler()
    {
        $subject = new DnsRecords(null, new RecordFactory());
        $this->assertEquals($subject->getHandler(), new TCP());
    }

    public function testInjectNullFactory()
    {
        $subject = new DnsRecords(new DnsGetRecord(), null);
        $this->assertEquals($subject->getFactory(), new RecordFactory());
    }

    public function testInjectNullDependencies()
    {
        $subject = new DnsRecords(null, null);
        $this->assertEquals($subject->getHandler(), new TCP());
        $this->assertEquals($subject->getFactory(), new RecordFactory());
    }

    public function testReturnSameHandler()
    {
        $handler = new DnsGetRecord();
        $subject = new DnsRecords($handler, null);
        $this->assertSame($handler, $subject->getHandler());
    }

    public function testReturnSameFactory()
    {
        $factory = new RecordFactory();
        $subject = new DnsRecords(null, $factory);
        $this->assertSame($factory, $subject->getFactory());
    }

    public function testSetHandler()
    {
        $handler = new DnsGetRecord();
        $subject = new DnsRecords(null, null);
        $subject->setHandler($handler);
        $this->assertSame($handler, $subject->getHandler());
    }

    public function testSetFactory()
    {
        $factory = new RecordFactory();
        $subject = new DnsRecords(null, null);
        $subject->setFactory($factory);
        $this->assertSame($factory, $subject->getFactory());
    }

}
