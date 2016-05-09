<?php

class WrongMethod implements Request
{
    private $data;

    /**
     * @param array $data
     * @return void
     */
    public function setData ( $data ) {
        $this->data = $data;
    }

    /**
     * @return void
     */
    public function execute () {

    }

    /**
     * @return array
     */
    public function result () {
        return ErrorBuilder::getBuilder ()
                           ->setError ( ErrorBuilder::WRONG_METHOD )
                           ->setObject ( isset( $this->data[ "m" ] ) ? $this->data[ "m" ] : "VOID" )
                           ->result ();
    }
}