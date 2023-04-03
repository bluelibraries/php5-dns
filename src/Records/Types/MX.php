<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class MX extends AbstractRecord
{

    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::MX;
    }

    /**
     * @return string|null
     */
    public function getTarget()
    {
        return isset($this->data['target']) ? $this->data['target'] : null;
    }

    /**
     * @return int|null
     */
    public function getPriority()
    {
        return isset($this->data['pri']) ? (int)$this->data['pri'] : null;
    }

}
