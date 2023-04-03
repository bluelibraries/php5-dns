<?php

namespace BlueLibraries\PHP5\Dns\Handlers;

class DnsHandlerTypes
{
    const DNS_GET_RECORD = 'dnsGetRecord'; // PHP internal function
    const DIG = 'dig'; // dig command if available on local machine
    const TCP = 'tcp'; // direct TCP connection to a DNS server
    const UDP = 'udp'; // direct UDP connection to a DNS server

    /**
     * @var array|string[]
     */
    protected static $all = [
        self::TCP,
        self::DNS_GET_RECORD,
        self::DIG,
        self::UDP,
    ];

    /**
     * @return array|string[]
     */
    public static function getAll()
    {
        return self::$all;
    }

}
