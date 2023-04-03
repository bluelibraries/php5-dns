<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class PTR extends AbstractRecord
{

    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::PTR;
    }

    /**
     * @return string|null
     */
    public function getTarget()
    {
        return isset($this->data['target']) ? $this->data['target'] : null;
    }

}
