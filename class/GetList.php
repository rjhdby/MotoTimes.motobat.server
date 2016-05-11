<?php

class GetList implements Request
{
    private $result;

    public function execute () {
        $db = new DB();
        if ( $db->connect_errno ) {
            $this->result = ErrorBuilder::dbError ( $db->connect_error );
            return;
        }
        $query  = /** @lang MySQL */
            '
          SELECT
              id,
              UNIX_TIMESTAMP(created) AS created,
              lat,
              lng AS lon,
              karma,
              alignment,
              CASE transport
              WHEN 1 THEN "GS"
              WHEN 2 THEN "RT"
              WHEN 2 THEN "CAR"
              END AS type,
              `text`
          FROM
              mototimes_events
          WHERE
              UNIX_TIMESTAMP() - UNIX_TIMESTAMP(created) < 14400 + karma*60
              AND transport IN (1,2,3)
          UNION ALL
          SELECT
              id,
              0 AS created,
              lat,
              lon,
              0 AS karma,
              0 AS alignment,
              type,
              `text`
          FROM
              objects
          WHERE `stop` IS NULL OR `stop`>CURRENT_TIMESTAMP()
              ';
        $result = $db->query ( $query );
        if ( $db->errno !== 0 ) {
            $this->result = ErrorBuilder::dbError ( $db->error );
            return;
        }
        $list = array ();
        while ( $row = $result->fetch_assoc () ) {
            $list[] = array (
                'id' => (int) $row[ 'id' ],
                'created' => (int) $row[ 'created' ],
                'lat' => (double) $row[ 'lat' ],
                'lon' => (double) $row[ 'lon' ],
                'karma' => (int) $row[ 'karma' ],
                'alignment' => (int) $row[ 'alignment' ],
                'type' => $row[ 'type' ],
                'text' => $row[ 'text' ]
            );
        }
        $db->close ();
        $this->result = array ( "error" => "OK", "data" => $list );
    }

    public function setData ( $data ) {
    }

    public function result () {
        return $this->result;
    }
}