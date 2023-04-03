<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Records;

use BlueLibraries\PHP5\Dns\Records\RecordException;
use BlueLibraries\PHP5\Dns\Records\RecordFactory;
use BlueLibraries\PHP5\Dns\Records\RecordInterface;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use PHPUnit\Framework\TestCase;

class RecordFactoryTest extends TestCase
{

    /** 
  @var RecordFactory $subject; 
**/ 
 protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = new RecordFactory();
    }

    public static function allRecordTypesFormattedClassesDataProvider()
    {
        return require dirname(__FILE__) . "/../Data/allRecordsTypesFormattedClasses.php";
    }

    /**
     * @return void
     * @dataProvider allRecordTypesFormattedClassesDataProvider
     * @throws RecordException
     */
    public function testCreateDefaultRecords(array $data, $class, $classExtended)
    {
        $record = $this->subject->create($data, false);
        $this->assertSame(get_class($record), $class);
        $this->assertSame($data, $record->toArray());
    }

    /**
     * @return void
     * @dataProvider allRecordTypesFormattedClassesDataProvider
     * @throws RecordException
     */
    public function testCreateExtendedRecords(array $data, $class, $classExtended)
    {
        $record = $this->subject->create($data, true);
        $this->assertSame(get_class($record), $classExtended);
        $this->assertSame($data, $record->toArray());
    }

    /**
     * @return void
     * @throws RecordException
     * @expectedException BlueLibraries\PHP5\Dns\Records\RecordException
     * @expectedExceptionMessage Invalid record type for recordData: []
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Records\RecordException::UNABLE_TO_CREATE_RECORD
     */
    public function testCreateMissingRecordTypeThrowsException()
    {
        $this->subject->create([], false);
    }

    /**
     * @return void
     * @throws RecordException
     * @expectedException BlueLibraries\PHP5\Dns\Records\RecordException
     * @expectedExceptionMessage Invalid record type for recordData: {"type":"INVALID"}
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Records\RecordException::UNABLE_TO_CREATE_RECORD
     */
    public function testCreateInvalidRecordTypeThrowsException()
    {
        $this->subject->create(['type' => 'INVALID'], false);
    }

    public static function implementedRecordTypesDataProvider()
    {
        return [
            ['A'],
            ['NS'],
            ['CNAME'],
            ['SOA'],
            ['PTR'],
            ['HINFO'],
            ['MX'],
            ['TXT'],
            ['AAAA'],
            ['SRV'],
            ['NAPTR'],
            ['DS'],
            ['RRSIG'],
            ['NSEC'],
            ['DNSKEY'],
            ['NSEC3PARAM'],
            ['CDS'],
            ['CDNSKEY'],
            ['TYPE65'],
            ['CAA'],
            ['SPF'],
        ];
    }

    /**
     * @param $typeName
     * @return void
     * @dataProvider implementedRecordTypesDataProvider
     * @throws RecordException
     */
    public function testImplementedRecordCreation($typeName)
    {
        $this->assertInstanceOf(
            RecordInterface::class,
            $this->subject->create(
                [
                    'host' => 'test.com',
                    'ttl'  => 3600,
                    'type' => $typeName,
                ],
                true
            )
        );
    }

    public static function notImplementedRecordTypesDataProvider()
    {
        return
            array_map(
                function ($item) {
                    return [$item];
                },
                array_diff(
                    RecordTypes::getTypesNamesList(),
                    array_map(
                        function ($item) {
                            return $item[0];
                        },
                        static::implementedRecordTypesDataProvider()
                    )));
    }

    /**
     * @param $typeName
     * @return void
     * @throws RecordException
     * @dataProvider notImplementedRecordTypesDataProvider
     */
    public function testNotImplementedRecordCreation($typeName)
    {
        $this->assertNull(
            $this->subject->create(
                [
                    'host' => 'test.com',
                    'ttl'  => 3600,
                    'type' => $typeName,
                ],
                true
            ), "typeName:" . $typeName);
    }

}
