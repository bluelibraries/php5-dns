<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Records;

use BlueLibraries\PHP5\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\DKIM;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\DMARC;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\DomainVerification;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\SPF;
use PHPUnit\Framework\TestCase;

class ExtendedTxtRecordsTest extends TestCase
{

    /** 
  @var ExtendedTxtRecords $subject; 
**/ 
 private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = new ExtendedTxtRecords();
    }

    public static function isTxtRecordDataProvider()
    {
        return [
            [[], false],
            [['type' => 'A'], false],
            [['type' => 'txt'], false],
            [['type' => 'TXT'], true],
            [['ttl' => 3600, 'type' => 'TXT'], true],
            [['host' => 'test.com', 'ttl' => 3600, 'type' => 'TXT'], true],
            [['host' => 'test.com', 'ttl' => 3600, 'type' => 'TXT', 'txt' => ''], true],
        ];
    }

    /**
     * @param array $data
     * @param $expected
     * @dataProvider isTxtRecordDataProvider
     * @return void
     */
    public function testIsTxtRecord(array $data, $expected)
    {
        $this->assertSame($expected, $this->subject->isTxtRecord($data));
    }

    public static function getExtendedTxtRecordsDataProvider()
    {
        return [
            [[], null],
            [['type' => 'TEST'], null],
            [['type' => 'TXT'], null],
            [['host' => 'sub.test.com', 'type' => 'TXT', 'txt' => 'docusign=test123'], DomainVerification::class],
            [['host' => 'test.com', 'type' => 'TXT', 'txt' => 'v=spf1 include:test.com'], SPF::class],
            [['host' => 'zacusca._domainkey.test.com', 'type' => 'TXT', 'txt' => 'v=DKIM1; p=publickey;h=a; g=oo; n=a;q=t;s=x; t=0'], DKIM::class],
            [['host' => '_dmarc.test.com', 'type' => 'TXT', 'txt' => 'v=DMARC1; p=quarantine;pct=75; rua=mailto:postmaster@test.com; ruf=mailto:ruf@test.com; sp=reject;fo=d; aspf=s;adkim=r; rf=afrf;ri=86400 '], DMARC::class],
        ];
    }

    /**
     * @dataProvider getExtendedTxtRecordsDataProvider
     * @return void
     */
    public function testGetExtendedTxtRecord(array $data, $expected)
    {
        $record = $this->subject->getExtendedTxtRecord($data);
        if (is_null($expected)) {
            $this->assertSame($record, $expected);
        } else {
            $this->assertSame(get_class($record), $expected);
        }
    }

}
