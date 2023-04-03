<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class SRV extends AbstractRecord
{

    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::SRV;
    }

    /**
     * @return int|null
     */
    public function getPriority()
    {
        return isset($this->data['pri']) ? (int)$this->data['pri'] : null;
    }

    /**
     * @return int|null
     */
    public function getWeight()
    {
        return isset($this->data['weight']) ? (int)$this->data['weight'] : null;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return isset($this->data['port']) ? (int)$this->data['port'] : null;
    }

    /**
     * @return string|null
     */
    public function getTarget()
    {
        return isset($this->data['target']) ? $this->data['target'] : null;
    }

}
