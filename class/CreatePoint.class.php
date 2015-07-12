<?php


class CreatePoint extends Core
{
    function __construct ( $data ) {
        parent::__construct ( $data );
        $this->setPrerequisites ( 'userid,lat,lng,name' );
        if ( $this->checkPrerequisites () ) {
            $this->addPoint ();
        }
    }

    private function addPoint () {
        $role = new Role( $this->getData () );
        $role->readRole ();
        if ( $role->getRole () == 'readonly' ) {
            $this->noRightsError ();
        } else {
            $db    = new DB();
            $query = /** @lang MySQL */
                'INSERT INTO mototimes_events (userid, lat, lng, name) VALUES(?,?,?,?)';
            $stmt  = $db->prepare ( $query );
            $stmt->bind_param ( 'idds', $this->get ( 'userid' ), $this->get ( 'lat' ), $this->get ( 'lng' ) , $this->get('name'));
            $stmt->execute ();
            if ( $stmt->errno != 0 ) {
                $this->unknownError ();
            } else {
                $result = array (
                    'response' => 'ok'
                );
                $this->setResult ( $result );
            }

        }
    }

    private function noRightsError () {
        $this->setError ();
        $this->setErrorText ( "NO RIGHTS" );
        $this->setErrorObject ( 'readonly' );
    }

    private function unknownError () {
        $this->setError ();
        $this->setErrorText ( "UNKNOWN ERROR" );
        $this->setErrorObject ( 'void' );
    }
}