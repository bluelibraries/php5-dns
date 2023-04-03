<?php

namespace BlueLibraries\PHP5\Dns\Handlers;

use BlueLibraries\PHP5\Dns\Handlers\Types\Dig;
use BlueLibraries\PHP5\Dns\Handlers\Types\DnsGetRecord;
use BlueLibraries\PHP5\Dns\Handlers\Types\TCP;
use BlueLibraries\PHP5\Dns\Handlers\Types\UDP;

class DnsHandlerFactory
{
    /**
     * @param string $handlerType
     * @return DnsHandlerInterface
     * @throws DnsHandlerFactoryException
     */
    public function create($handlerType)
    {
        switch ($handlerType) {

            case DnsHandlerTypes::DNS_GET_RECORD:
                return new DnsGetRecord();

            case DnsHandlerTypes::DIG:
                return new Dig();

            case DnsHandlerTypes::TCP:
                return new TCP();

            case DnsHandlerTypes::UDP:
                return new UDP();

            default:
                throw new DnsHandlerFactoryException(
                    'Unable to build handler type: ' . json_encode($handlerType),
                    DnsHandlerFactoryException::ERR_UNABLE_TO_CREATE_HANDLER_TYPE
                );

        }
    }
}
