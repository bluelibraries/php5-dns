<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class HINFO extends AbstractRecord
{
    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::HINFO;
    }

    /**
     * @return string|null
     */
    public function getHardware()
    {
        return isset($this->data['hardware']) ? $this->data['hardware'] : null;
    }

    /**
     * @return string|null
     */
    public function getOperatingSystem()
    {
        return isset($this->data['os']) ? $this->data['os'] : null;
    }

}
