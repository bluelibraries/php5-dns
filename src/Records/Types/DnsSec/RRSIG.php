<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\DnsSec;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class RRSIG extends AbstractRecord
{
    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::RRSIG;
    }

    /**
     * @return string|null
     */
    public function getTypeCovered()
    {
        return isset($this->data['type-covered']) ? $this->data['type-covered'] : null;
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
    public function getLabelsNumber()
    {
        return isset($this->data['labels-number']) ? $this->data['labels-number'] : null;
    }

    /**
     * @return int|null
     */
    public function getOriginalTtl()
    {
        return isset($this->data['original-ttl']) ? $this->data['original-ttl'] : null;
    }

    /**
     * @return int|null
     */
    public function getExpiration()
    {
        return isset($this->data['signature-expiration']) ? $this->data['signature-expiration'] : null;
    }

    /**
     * @return int|null
     * @meta same with Inception
     */
    public function getCreation()
    {
        return isset($this->data['signature-creation']) ? $this->data['signature-creation'] : null;
    }

    /**
     * @return int|null
     */
    public function getTag()
    {
        return isset($this->data['key-tag']) ? $this->data['key-tag'] : null;
    }

    /**
     * @return string|null
     */
    public function getSignerName()
    {
        return isset($this->data['signer-name']) ? $this->data['signer-name'] : null;
    }

    /**
     * @return string|null
     */
    public function getSignature()
    {
        return isset($this->data['signature']) ? $this->data['signature'] : null;
    }

}
