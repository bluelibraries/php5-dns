<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Records\Types\Txt;

use BlueLibraries\PHP5\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\DKIM;
use PHPUnit\Framework\TestCase;

class DKIMTest extends TestCase
{

    /**
     * @var DKIM $subject ;
     **/
    protected $subject;

    public function setUp()
    {
        $this->subject = new DKIM([]);
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

    public function testGetTxtValue()
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
        $this->assertSame(ExtendedTxtRecords::DKIM, $this->subject->getTypeName());
    }

    /**
     * @return array[]
     */
    public static function parseValuesDataProvider()
    {
        return [
            ['', false],
            ['p', false],
            ['p=publickey', false],
            ['v=DKIM1; ', false],
            ['v=DKIM1; p=publickey', true]
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
                'host'  => 'zacusca.domainkey.test.com',
                'txt'   => $txt
            ]
        );

        $this->assertSame($expected, $this->subject->parseValues());
    }

    /**
     * @return array[]
     */
    public static function valuesDataProvider()
    {
        return [
            ['', []],
            ['p=publick; ', ['p' => 'publick']],
            ['v=DKIM1; ', ['v' => 'DKIM1']],
            ['v=DKIM1; p=publickey', ['v' => 'DKIM1', 'p' => 'publickey']],
            [
                'v=DKIM1; k=rsa; p=publickey;h=a; g=oo; n=a;q=t;s=x; t=0',
                [
                    'v' => 'DKIM1',
                    'k' => 'rsa',
                    'p' => 'publickey',
                    'h' => 'a',
                    'g' => 'oo',
                    'n' => 'a',
                    'q' => 't',
                    's' => 'x',
                    't' => '0'
                ]],
        ];
    }

    /**
     * @return string[]
     */
    private function getKeyValues()
    {
        return ['v', 'k', 'p', 'h', 'g', 'n', 'q', 's', 't',];
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
                'host'  => 'zacusca.domainkey.test.com',
                'txt'   => $txt
            ]
        );

        $keyValues = $this->getKeyValues();

        foreach ($keyValues as $key) {

            $expectedValue = isset($expected[$key]) ? $expected[$key] : null;

            switch ($key) {

                case DKIM::VERSION:
                    $this->assertSame($expectedValue, $this->subject->getVersion());
                    break;

                case DKIM::KEY_TYPE:
                    $this->assertSame($expectedValue, $this->subject->getKeyType());
                    break;

                case DKIM::PUBLIC_KEY:
                    $this->assertSame($expectedValue, $this->subject->getPublicKey());
                    break;

                case DKIM::HASH_TYPE:
                    $this->assertSame($expectedValue, $this->subject->getHashType());
                    break;

                case DKIM::GROUP:
                    $this->assertSame($expectedValue, $this->subject->getGroup());
                    break;

                case DKIM::NOTES:
                    $this->assertSame($expectedValue, $this->subject->getNotes());
                    break;

                case DKIM::QUERY:
                    $this->assertSame($expectedValue, $this->subject->getQuery());
                    break;

                case DKIM::SERVICE_TYPE:
                    $this->assertSame($expectedValue, $this->subject->getServiceType());
                    break;

                case DKIM::TESTING_TYPE:
                    $this->assertSame($expectedValue, $this->subject->getTestingType());
                    break;

            }
        }
    }

    public function testInvalidSelector()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => 'zacusca.domainkey.test.com',
                'txt'   => ''
            ]
        );

        $this->assertNull($this->subject->getSelector());
    }

    public function testEmptySelector()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'txt'   => ''
            ]
        );

        $this->assertNull($this->subject->getSelector());
    }

    public function testValidSelector()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => 'zacusca._domainkey.test.com',
                'txt'   => ''
            ]
        );

        $this->assertSame('zacusca', $this->subject->getSelector());
    }

}
