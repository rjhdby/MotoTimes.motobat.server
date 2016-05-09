<?php

interface Request
{
    /**
     * @param array $data
     * @return void
     */
    public function setData ( $data );

    /**
     * @return void
     */
    public function execute ();

    /**
     * @return array
     */
    public function result ();
}