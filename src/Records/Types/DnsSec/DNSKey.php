<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\DnsSec;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class DNSKey extends AbstractRecord
{
    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::DNSKEY;
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
    public function getProtocol()
    {
        return isset($this->data['protocol']) ? $this->data['protocol'] : null;
    }

    /**
     * @return int|null
     */
    public function getAlgorithm()
    {
        return isset($this->data['algorithm']) ? $this->data['algorithm'] : null;
    }

    /**
     * @return string|null
     */
    public function getPublicKey()
    {
        return isset($this->data['public-key']) ? $this->data['public-key'] : null;
    }

}
