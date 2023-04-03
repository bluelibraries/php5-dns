<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Records\Types\Txt;

use BlueLibraries\PHP5\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\MtaSts;
use PHPUnit\Framework\TestCase;

class MtaStsTest extends TestCase
{

    /**
     * @var MtaSts $subject ;
     **/
    protected $subject;

    public function setUp()
    {
        $this->subject = new MtaSts([]);
        parent::setUp();
    }

    public function testGetTypeId()
    {
        $this->assertSame(RecordTypes::TXT, $this->subject->getTypeId());
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

    public function testGetTxt()
    {
        $this->assertNull($this->subject->getTxt());
    }

    public function testGetIpValue()
    {
        $value = 'random text here';
        $this->subject->setData(['txt' => $value]);
        $this->assertSame($value, $this->subject->getTxt());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN TXT', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'ttl'  => 7200,
                'host' => 'test.com',
                'txt'  => 'text here'
            ]
        );
        $this->assertSame('test.com 7200 IN TXT "text here"', $this->subject->toString());
    }

    public function testToStringCompleteWithChaosClass()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'CH',
                'host'  => 'test.com',
                'txt'   => 'text here'
            ]
        );
        $this->assertSame('test.com 7200 CH TXT "text here"', $this->subject->toString());
    }

    public function testGetEmptyText()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => 'test.com',
                'txt'   => ''
            ]
        );
        $this->assertSame('test.com 7200 IN TXT ""', $this->subject->toString());
    }

    public function testGetExtendedTypeName()
    {
        $this->assertSame(ExtendedTxtRecords::MTA_STS_REPORTING, $this->subject->getTypeName());
    }

    public static function parseValuesDataProvider()
    {
        return [
            ['', false],
            ['p', false],
            ['v=DMARC1; ', false],
            ['id=test1234', false],
            ['v=STSv1; ', false],
            ['v=STSv1; rua=', false],
            ['v=STSv;id=test1234', false],
            ['v=STSv1; id=test1234', true]
        ];
    }

    /**
     * @param $txt
     * @param $expected
     * @dataProvider parseValuesDataProvider
     * @return void
     */
    public function testParseValues($txt, $expected)
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => '_mta-sts.test.com',
                'txt'   => $txt
            ]
        );

        $this->assertSame($expected, $this->subject->parseValues());
    }

    public static function valuesDataProvider()
    {
        return [
            ['', []],
            ['p=reject; ', ['p' => 'reject']],
            ['v=STSv1; ', ['v' => 'STSv1']],
            ['v=STSv1; id=none', ['v' => 'STSv1', 'id' => 'none']],
            [
                'v=STSv1; id=test4321',
                [
                    'v'  => 'STSv1',
                    'id' => 'test4321',
                ]],
        ];
    }

    /**
     * @return string[]
     */
    private function getKeyValues()
    {
        return ['v', 'id'];
    }

    /**
     * @param $txt
     * @param array $expected
     * @dataProvider valuesDataProvider
     * @return void
     */
    public function testValues($txt, array $expected)
    {

        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => '_mta-sts.test.com',
                'txt'   => $txt
            ]
        );

        $keyValues = $this->getKeyValues();

        foreach ($keyValues as $key) {
            $expectedValue = isset($expected[$key]) ? $expected[$key] : null;

            switch ($key) {

                case MtaSts::VERSION:
                    $this->assertSame($expectedValue, $this->subject->getVersion());
                    break;

                case MtaSts::ID:
                    $this->assertSame($expectedValue, $this->subject->getId());
                    break;

            }
        }
    }

}
