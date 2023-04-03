<?php

namespace BlueLibraries\PHP5\Dns\Records;

class RecordTypes
{

    const ALL = 255;
    const A = 1;
    const NS = 2;

    /** @deprecated Use MX instead */
    const MD = 3;
    /** @deprecated Use MX instead */
    const MF = 4;

    const CNAME = 5;
    const SOA = 6;

    /** @meta Experimental */
    const MB = 7;
    const MG = 8;
    const MR = 9;

    /** @meta Experimental */
    const NULL = 10;

    const WKS = 11;
    const PTR = 12;
    const HINFO = 13;
    const MINFO = 14;
    const MX = 15;
    const TXT = 16;
    const RP = 17;
    const AFSDB = 18;
    const X25 = 19;
    const ISDN = 20;
    const RT = 21;
    const NSAP = 22;
    const NSAP_PTR = 23;
    const SIG = 24;
    const KEY = 25;
    const PX = 26;
    const GPOS = 27;
    const AAAA = 28;
    const LOC = 29;

    /**
     * @deprecated OBSOLETE
     */
    const NXT = 30;

    const EID = 31;
    const NIMLOC = 32;
    const SRV = 33;
    const ATMA = 34;
    const NAPTR = 35;
    const KX = 36;
    const CERT = 37;

    /**
     * @deprecated OBSOLETE, use AAAA instead
     */
    const A6 = 38;
    const DNAME = 39;
    const SINK = 40;
    const OPT = 41;
    const APL = 42;
    const DS = 43;
    const SSHFP = 44;
    const IPSECKEY = 45;
    const RRSIG = 46;
    const NSEC = 47;
    const DNSKEY = 48;
    const DHCID = 49;
    const NSEC3 = 50;
    const NSEC3PARAM = 51;
    const TLSA = 52;
    const SMIMEA = 53;
    // 54 is unassigned at this moment
    const HIP = 55;
    const NINFO = 56;
    const RKEY = 57;
    const TALINK = 58;
    const CDS = 59;
    const CDNSKEY = 60;
    const OPENPGPKEY = 61;
    const CSYNC = 62;
    const ZONEMD = 63;
    const SVCB = 64;
    const HTTPS = 65;
    const TKEY = 249;
    const TSIG = 250;
    const IXFR = 251;
    const AXFR = 252;
    const MAILB = 253;
    const MAILA = 254;
    const URI = 256;
    const CAA = 257;
    const AVC = 258;
    const DOA = 259;
    const AMTRELAY = 260;
    const TA = 32768;
    const DLV = 32769;

    /**
     * @deprecated OBSOLETE, use SPF instead (TXT record)
     */
    const DEPRECATED_SPF = 99;

    /**
     * @deprecated, not a standard, yet
     */
    const TYPE_65 = -1;

    /**
     * @var string[]
     */
    private static $all = [
        self::ALL        => 'ANY',
        self::A          => 'A',
        self::NS         => 'NS',
        self::MD         => 'MD',
        self::MF         => 'MF',
        self::CNAME      => 'CNAME',
        self::SOA        => 'SOA',
        self::MB         => 'MB',
        self::MG         => 'MG',
        self::MR         => 'MR',
        self::NULL       => 'NULL',
        self::WKS        => 'WKS',
        self::PTR        => 'PTR',
        self::HINFO      => 'HINFO',
        self::MINFO      => 'MINFO',
        self::MX         => 'MX',
        self::TXT        => 'TXT',
        self::RP         => 'RP',
        self::AFSDB      => 'AFSDB',
        self::X25        => 'X25',
        self::ISDN       => 'ISDN',
        self::RT         => 'RT',
        self::NSAP       => 'NSAP',
        self::NSAP_PTR   => 'NSAP-PTR',
        self::SIG        => 'SIG',
        self::KEY        => 'KEY',
        self::PX         => 'PX',
        self::GPOS       => 'GPOS',
        self::AAAA       => 'AAAA',
        self::LOC        => 'LOC',
        self::NXT        => 'NXT',
        self::EID        => 'EID',
        self::NIMLOC     => 'NIMLOC',
        self::SRV        => 'SRV',
        self::ATMA       => 'ATMA',
        self::NAPTR      => 'NAPTR',
        self::KX         => 'KX',
        self::CERT       => 'CERT',
        self::A6         => 'A6',
        self::DNAME      => 'DNAME',
        self::SINK       => 'SINK',
        self::OPT        => 'OPT',
        self::APL        => 'APL',
        self::DS         => 'DS',
        self::SSHFP      => 'SSHFP',
        self::IPSECKEY   => 'IPSECKEY',
        self::RRSIG      => 'RRSIG',
        self::NSEC       => 'NSEC',
        self::DNSKEY     => 'DNSKEY',
        self::DHCID      => 'DHCID',
        self::NSEC3      => 'NSEC3',
        self::NSEC3PARAM => 'NSEC3PARAM',
        self::TLSA       => 'TLSA',
        self::SMIMEA     => 'SMIMEA',
        self::HIP        => 'HIP',
        self::NINFO      => 'NINFO',
        self::RKEY       => 'RKEY',
        self::TALINK     => 'TALINK',
        self::CDS        => 'CDS',
        self::CDNSKEY    => 'CDNSKEY',
        self::OPENPGPKEY => 'OPENPGPKEY',
        self::CSYNC      => 'CSYNC',
        self::ZONEMD     => 'ZONEMD',
        self::SVCB       => 'TYPE64',
        self::HTTPS      => 'TYPE65',
        self::TKEY       => 'TKEY',
        self::TSIG       => 'TSIG',
        self::IXFR       => 'IXFR',
        self::AXFR       => 'AXFR',
        self::MAILB      => 'MAILB',
        self::MAILA      => 'MAILA',
        self::URI        => 'URI',
        self::CAA        => 'CAA',
        self::AVC        => 'AVC',
        self::DOA        => 'DOA',
        self::AMTRELAY   => 'AMTRELAY',
        self::TA         => 'TA',
        self::DLV        => 'DLV',

        self::DEPRECATED_SPF => 'SPF',

    ];

    /**
     * @var array
     */
    private static $types = [];

    /**
     * @param int $type
     * @return string|null
     */
    public static function getName($type)
    {
        return isset(static::$all[$type]) ? static::$all[$type] : null;
    }

    /**
     * @param $name
     * @return int|null
     */
    public static function getType($name)
    {
        self::initTypesIfNeeded();
        return isset(self::$types[$name]) ? self::$types[$name] : null;
    }

    /**
     * @param int $typeId
     * @return bool
     */
    public static function isValidTypeId($typeId)
    {
        return isset(self::$all[$typeId]);
    }

    /**
     * @return void
     */
    private static function initTypesIfNeeded()
    {
        if (empty(static::$types)) {
            static::$types = array_flip(static::$all);
        }
    }

    /**
     * @return array
     */
    public static function getTypesNamesList()
    {
        return array_values(self::$all);
    }

}
