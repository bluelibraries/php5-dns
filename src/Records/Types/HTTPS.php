<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

/**
 * This is known also as TYPE65
 */
class HTTPS extends AbstractRecord
{
    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::HTTPS;
    }

    /**
     * @return string|null
     */
    public function getSeparator()
    {
        return isset($this->data['separator']) ? $this->data['separator'] : null;
    }

    /**
     * @return int|null
     */
    public function getOriginalLength()
    {
        return isset($this->data['original-length']) ? $this->data['original-length'] : null;
    }

    /**
     * @return string|null
     */
    public function getData()
    {
        return isset($this->data['data']) ? $this->data['data'] : null;
    }

}
