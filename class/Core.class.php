<?php

class Core
{
    private $prerequisites = array();
    private $error = false;
    private $errorText;
    private $errorObject = "";
    private $result;
    private $data;

    function __construct($data = array())
    {
        $this->data = $data;
    }

    protected function checkPrerequisites()
    {
        foreach ($this->prerequisites as $key) {
            if (!isset($this->data[ $key ])) {
                $this->setError();
                $this->setErrorText("PREREQUISITES");
                $this->setErrorObject($key);

                return false;
            }
        }

        return true;
    }

    public function isError()
    {
        return $this->error;
    }

    public function getErrorText()
    {
        return $this->errorText;
    }

    public function setError()
    {
        $this->error = true;
    }

    public function setErrorText($errorText)
    {
        $this->errorText = $errorText;
    }

    public function setPrerequisites($prerequisites)
    {
        if (is_array($prerequisites)) {
            $this->prerequisites = $prerequisites;
        } else {
            $this->prerequisites = explode(',', $prerequisites);
        }
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function get($key)
    {
        return $this->data[ $key ];
    }

    public function put($key, $value)
    {
        $this->data[ $key ] = $value;
    }

    public function getErrorObject()
    {
        return $this->errorObject;
    }

    public function setErrorObject($errorObject)
    {
        $this->errorObject = $errorObject;
    }
}