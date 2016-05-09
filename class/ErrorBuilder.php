<?php

class ErrorBuilder
{
    const WRONG_METHOD = "WRONG METHOD";
    const DB_ERROR     = "DB ERROR";
    const UNKNOWN      = "UNKNOWN";

    private $result = array ();

    private function __construct () {
        $this->setError ( ErrorBuilder::UNKNOWN );
    }

    /**
     * @param string $error
     * @return $this
     */
    public function setError ( $error = "" ) {
        $this->result[ "error" ] = $error;
        return $this;
    }

    /**
     * @return ErrorBuilder
     */
    public static function getBuilder () {
        return new ErrorBuilder();
    }

    /**
     * @param string $object
     * @return $this
     */
    public function setObject ( $object = "" ) {
        $this->result[ "object" ] = $object;
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText ( $text = "" ) {
        $this->result[ "text" ] = $text;
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addParameter ( $key = "key", $value = "" ) {
        $this->result[ $key ] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function result () {
        return $this->result;
    }

    /**
     * Preset for DB ERROR
     * @return array
     */
    public static function dbError ( $error ) {
        return self::getBuilder ()
                   ->setError ( self::DB_ERROR )
                   ->setObject ( $error )
                   ->result ();
    }
}