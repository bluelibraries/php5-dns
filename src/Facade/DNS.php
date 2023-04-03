<?php

namespace BlueLibraries\PHP5\Dns\Facade;

use BlueLibraries\PHP5\Dns\DnsRecords;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerFactory;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerFactoryException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\PHP5\Dns\Records\RecordException;
use BlueLibraries\PHP5\Dns\Records\RecordInterface;

class DNS
{
    /**
     * @var DnsHandlerFactory|null
     */
    private static $dnsHandlerFactory = null;

    /**
     * @return DnsHandlerFactory|null
     */
    private static function getHandlerFactory()
    {
        if (is_null(self::$dnsHandlerFactory)) {
            self::$dnsHandlerFactory = new DnsHandlerFactory();
        }
        return self::$dnsHandlerFactory;
    }

    /**
     * @param string $host
     * @param int|int[] $type
     * @param string|null $handlerType
     * @param bool|null $useExtendedRecords
     * @param string|null $nameserver
     * @return RecordInterface[]
     * @throws DnsHandlerException
     * @throws DnsHandlerFactoryException
     * @throws RecordException
     */
    public static function getRecords(
        $host,
        $type,
        $handlerType = DnsHandlerTypes::TCP,
        $useExtendedRecords = true,
        $nameserver = null)
    {
        $dnsHandler = self::getHandlerFactory()
            ->create($handlerType);

        if (!is_null($nameserver)) {
            $dnsHandler->setNameserver($nameserver);
        }

        return (new DnsRecords())
            ->setHandler(
                $dnsHandler
            )
            ->get($host, $type, $useExtendedRecords);
    }

}
