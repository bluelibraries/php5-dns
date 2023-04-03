<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\DnsSec;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class NSEC3PARAM extends AbstractRecord
{
    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::NSEC3PARAM;
    }

    /**
     * @return int|null
     */
    public function getAlgorithm()
    {
        return isset($this->data['algorithm']) ? $this->data['algorithm'] : null;
    }

    /**
     * @return int|null
     */
    public function getFlags()
    {
        return isset($this->data['flags']) ? $this->data['flags'] : null;
    }

    /**
     * @return int|null
     */
    public function getIterations()
    {
        return isset($this->data['iterations']) ? $this->data['iterations'] : null;
    }

    /**
     * @return string|null
     */
    public function getSalt()
    {
        return isset($this->data['salt']) ? $this->data['salt'] : null;
    }

}
