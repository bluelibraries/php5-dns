<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\DnsSec;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class NSEC extends AbstractRecord
{
    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::NSEC;
    }

    /**
     * @return string|null
     */
    public function getNextAuthoritativeName()
    {
        return isset($this->data['next-authoritative-name'])
            ? $this->data['next-authoritative-name']
            : null;
    }

    /**
     * @return string|null
     */
    public function getTypes()
    {
        return isset($this->data['types']) ? $this->data['types'] : null;
    }

}
