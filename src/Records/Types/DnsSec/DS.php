<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\DnsSec;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class DS extends AbstractRecord
{
    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::DS;
    }

    /**
     * @return int|null
     */
    public function getKeyTag()
    {
        return isset($this->data['key-tag']) ? $this->data['key-tag'] : null;
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
    public function getAlgorithmDigest()
    {
        return isset($this->data['algorithm-digest']) ? $this->data['algorithm-digest'] : null;
    }

    /**
     * @return string|null
     */
    public function getDigest()
    {
        return isset($this->data['digest']) ? $this->data['digest'] : null;
    }

}
