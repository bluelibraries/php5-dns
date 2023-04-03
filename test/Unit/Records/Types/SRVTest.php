<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Records\Types;

use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use BlueLibraries\PHP5\Dns\Records\Types\SRV;
use PHPUnit\Framework\TestCase;

class SRVTest extends TestCase
{

    /**
     * @var SRV $subject ;
     **/
    protected $subject;

    public function setUp()
    {
        $this->subject = new SRV([]);
        parent::setUp();
    }

    public function testGetTypeId()
    {
        $this->assertSame(RecordTypes::SRV, $this->subject->getTypeId());
    }

    public function testSetDataReturnsSameModel()
    {
        $this->assertSame(get_class($this->subject), get_class($this->subject->setData([])));
    }

    public function testGetHostDefaultNull()
    {
        $this->assertSame('', $this->subject->getHost());
    }

    public function testGetHostValue()
    {
        $value = 'test' . time() . '.com';
        $this->subject->setData(['host' => $value]);
        $this->assertSame($value, $this->subject->getHost());
    }

    public function testGetClass()
    {
        $this->assertSame('IN', $this->subject->getClass());
    }

    public function testGetClassValue()
    {
        $value = 'IN';
        $this->subject->setData(['class' => $value]);
        $this->assertSame($value, $this->subject->getClass());
    }

    public function testGetTtl()
    {
        $this->assertSame(0, $this->subject->getTtl());
    }

    public function testGetTtlValue()
    {
        $value = strval(time());
        $this->subject->setData(['ttl' => $value]);
        $this->assertSame((int)$value, $this->subject->getTtl());
    }

    public function testGetPriority()
    {
        $this->assertNull($this->subject->getPriority());
    }

    public function testGetPriorityValue()
    {
        $value = '10';
        $this->subject->setData(['pri' => $value]);
        $this->assertSame((int)$value, $this->subject->getPriority());
    }

    public function testGetWeight()
    {
        $this->assertNull($this->subject->getWeight());
    }

    public function testGetWeightValue()
    {
        $value = '10';
        $this->subject->setData(['weight' => $value]);
        $this->assertSame((int)$value, $this->subject->getWeight());
    }

    public function testGetPort()
    {
        $this->assertNull($this->subject->getPort());
    }

    public function testGetPortValue()
    {
        $value = '64';
        $this->subject->setData(['port' => $value]);
        $this->assertSame((int)$value, $this->subject->getPort());
    }


    public function testGetTarget()
    {
        $this->assertNull($this->subject->getTarget());
    }

    public function testGetTargetValue()
    {
        $value = 'test.target.com';
        $this->subject->setData(['target' => $value]);
        $this->assertSame($value, $this->subject->getTarget());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN SRV', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'host'   => 'srv.test.com',
                'pri'    => 1,
                'port'   => 10,
                'target' => '192.168.0.1',
                'weight' => 9,
            ]
        );
        $this->assertSame('srv.test.com 0 IN SRV 1 9 10 192.168.0.1', $this->subject->toString());
    }

}
