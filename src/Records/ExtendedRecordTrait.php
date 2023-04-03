<?php

namespace BlueLibraries\PHP5\Dns\Records;

trait ExtendedRecordTrait
{
    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->getExtendedTypeName();
    }

}
