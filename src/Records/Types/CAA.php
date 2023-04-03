<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class CAA extends AbstractRecord
{

    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::CAA;
    }

    /**
     * @return int|null
     */
    public function getFlags()
    {
        return isset($this->data['flags']) ? $this->data['flags'] : null;
    }

    /**
     * @return string|null
     */
    public function getTag()
    {
        return isset($this->data['tag']) ? $this->data['tag'] : null;
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return isset($this->data['value']) ? $this->data['value'] : null;
    }

}