<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class CNAME extends AbstractRecord
{

    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::CNAME;
    }

    /**
     * @return string|null
     */
    public function getTarget()
    {
        return isset($this->data['target']) ? $this->data['target'] : null;
    }

}
