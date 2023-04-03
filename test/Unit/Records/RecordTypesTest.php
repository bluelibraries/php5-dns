<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Records;

use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use PHPUnit\Framework\TestCase;

class RecordTypesTest extends TestCase
{

    public static function testGetNameInvalid()
    {
        static::assertNull(RecordTypes::getName(0));
    }

    public static function validTypesDataProvider()
    {
        return [
            [RecordTypes::A],
            [RecordTypes::CNAME],
            [RecordTypes::HINFO],
            [RecordTypes::CAA],
            [RecordTypes::MX],
            [RecordTypes::NS],
            [RecordTypes::PTR],
            [RecordTypes::SOA],
            [RecordTypes::TXT],
            [RecordTypes::AAAA],
            [RecordTypes::SRV],
            [RecordTypes::NAPTR],
            [RecordTypes::A6],
            [RecordTypes::ALL],
        ];
    }

    /**
     * @return void
     * @dataProvider validTypesDataProvider
     */
    public static function testGetNameValid($typeId)
    {
        static::assertTrue(is_string(RecordTypes::getName($typeId)));
    }

    public static function testGetTypesNamesList()
    {
        static::assertSame([
            0  => 'ANY',
            1  => 'A',
            2  => 'NS',
            3  => 'MD',
            4  => 'MF',
            5  => 'CNAME',
            6  => 'SOA',
            7  => 'MB',
            8  => 'MG',
            9  => 'MR',
            10 => 'NULL',
            11 => 'WKS',
            12 => 'PTR',
            13 => 'HINFO',
            14 => 'MINFO',
            15 => 'MX',
            16 => 'TXT',
            17 => 'RP',
            18 => 'AFSDB',
            19 => 'X25',
            20 => 'ISDN',
            21 => 'RT',
            22 => 'NSAP',
            23 => 'NSAP-PTR',
            24 => 'SIG',
            25 => 'KEY',
            26 => 'PX',
            27 => 'GPOS',
            28 => 'AAAA',
            29 => 'LOC',
            30 => 'NXT',
            31 => 'EID',
            32 => 'NIMLOC',
            33 => 'SRV',
            34 => 'ATMA',
            35 => 'NAPTR',
            36 => 'KX',
            37 => 'CERT',
            38 => 'A6',
            39 => 'DNAME',
            40 => 'SINK',
            41 => 'OPT',
            42 => 'APL',
            43 => 'DS',
            44 => 'SSHFP',
            45 => 'IPSECKEY',
            46 => 'RRSIG',
            47 => 'NSEC',
            48 => 'DNSKEY',
            49 => 'DHCID',
            50 => 'NSEC3',
            51 => 'NSEC3PARAM',
            52 => 'TLSA',
            53 => 'SMIMEA',
            54 => 'HIP',
            55 => 'NINFO',
            56 => 'RKEY',
            57 => 'TALINK',
            58 => 'CDS',
            59 => 'CDNSKEY',
            60 => 'OPENPGPKEY',
            61 => 'CSYNC',
            62 => 'ZONEMD',
            63 => 'TYPE64',
            64 => 'TYPE65',
            65 => 'TKEY',
            66 => 'TSIG',
            67 => 'IXFR',
            68 => 'AXFR',
            69 => 'MAILB',
            70 => 'MAILA',
            71 => 'URI',
            72 => 'CAA',
            73 => 'AVC',
            74 => 'DOA',
            75 => 'AMTRELAY',
            76 => 'TA',
            77 => 'DLV',
            78 => 'SPF',
        ], RecordTypes::getTypesNamesList());
    }

}

