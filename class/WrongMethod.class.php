<?php

class WrongMethod
    extends Core
{
    function __construct($data)
    {
        parent::__construct($data);
        $this->setError();
        $this->setErrorText("WRONG METHOD");
        $this->setErrorObject(implode(',',$_POST));
    }
}