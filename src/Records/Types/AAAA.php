<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class AAAA extends AbstractRecord
{

    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::AAAA;
    }

    /**
     * @return string|null
     */
    public function getIPV6()
    {
        return isset($this->data['ipv6']) ? $this->data['ipv6'] : null;
    }

}