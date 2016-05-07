<?php

/**
 * Created by PhpStorm.
 * User: rjhdby
 * Date: 06.05.16
 * Time: 12:50
 */
class GetObjects extends Core
{
    function __construct()
    {
        parent::__construct(array());
    }

    public function getObjects()
    {
        $db = new DB();
        $query = /** @lang MySQL */
            '
          SELECT
              id,
              lat,
              lon,
              type,
              `text`
          FROM
              objects
          WHERE `stop` IS NULL OR `stop`>CURRENT_TIMESTAMP()
              ';
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $list = array();
        if ($result->num_rows == 0) {
            $this->noActualEventsError();
        } else {
            while ($row = $result->fetch_assoc()) {
                $list[] = array(
                    'id' => $row['id'],
                    'type' => $row['type'],
                    'lat' => $row['lat'],
                    'lon' => $row['lon'],
                    'text' => $row['text']
                );
            }
        }
        $this->setResult($list);
    }

    private function noActualEventsError()
    {
        $this->setError();
        $this->setErrorText("NO ACTUAL EVENTS");
        $this->setErrorObject($this->get('void'));
    }
}