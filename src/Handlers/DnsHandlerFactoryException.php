<?php

namespace BlueLibraries\PHP5\Dns\Handlers;

use Exception;

class DnsHandlerFactoryException extends Exception
{
    const ERR_UNABLE_TO_CREATE_HANDLER_TYPE = 1;
}

