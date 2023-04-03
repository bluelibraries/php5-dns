<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class NAPTR extends AbstractRecord
{

    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::NAPTR;
    }

    /**
     * @return int|null
     */
    public function getOrder()
    {
        return isset($this->data['order']) ? $this->data['order'] : null;
    }

    /**
     * @return int|null
     */
    public function getPreference()
    {
        return isset($this->data['pref']) ? $this->data['pref'] : null;
    }

    /**
     * @return string|null
     */
    public function getFlag()
    {
        return isset($this->data['flag']) ? $this->data['flag'] : null;
    }

    /**
     * @return string|null
     */
    public function getServices()
    {
        return isset($this->data['services']) ? $this->data['services'] : null;
    }

    /**
     * @return string|null
     */
    public function getRegex()
    {
        return isset($this->data['regex']) ? $this->data['regex'] : null;
    }

    /**
     * @return string|null
     */
    public function getReplacement()
    {
        return isset($this->data['replacement']) ? $this->data['replacement'] : null;
    }

}
