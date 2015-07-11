<?php

class GetList extends Core
{
    function __construct () {
        parent::__construct ( array () );
    }

    public function getlist () {
        $db    = new DB();
        $query = /** @lang MySQL */
            '
          SELECT
              id,
              owner,
              UNIX_TIMESTAMP(created) AS created,
              lat,
              lng,
              karma
          FROM
              mototimes_events
          WHERE
              UNIX_TIMESTAMP() - UNIX_TIMESTAMP(created) < 14400 + karma*60
              ';
        $stmt  = $db->prepare ( $query );
        $stmt->execute ();
        $result = $stmt->get_result ();
        $list   = array ();
        if ( $result->num_rows == 0 ) {
            $this->noActualEventsError ();
        } else {
            while ( $row = $result->fetch_assoc () ) {
                $list[] = array (
                    'id' => $row[ 'id' ],
                    'owner' => $row[ 'owner' ],
                    'created' => $row[ 'created' ],
                    'lat' => $row[ 'lat' ],
                    'lng' => $row[ 'lng' ],
                    'karma' => $row[ 'karma' ]
                );
            }
        }
        $this->setResult ( $list );
    }

    private function noActualEventsError () {
        $this->setError ();
        $this->setErrorText ( "NO ACTUAL EVENTS" );
        $this->setErrorObject ( $this->get ( 'void' ) );
    }
}