<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class A extends AbstractRecord
{

    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::A;
    }

    /**
     * @return string|null
     */
    public function getIp()
    {
        return isset($this->data['ip']) ? $this->data['ip'] : null;
    }

}
