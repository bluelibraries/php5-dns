<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class TXT extends AbstractRecord
{

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::TXT;
    }

    /**
     * @return mixed|null
     */
    public function getTxt()
    {
        return isset($this->data['txt']) ? $this->data['txt'] : null;
    }

    /**
     * @param array $data
     * @return self
     */
    public function setData(array $data)
    {
        if (isset($data['entries'])) {
            unset($data['entries']);
        }
        return parent::setData($data);
    }

}
