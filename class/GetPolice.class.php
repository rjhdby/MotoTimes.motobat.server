<?php

class GetPolice
    extends Core
{
    function __construct()
    {
        parent::__construct(array());
    }

    public function getList()
    {
        $db = new DB();
        $query = /** @lang MySQL */
            '
          SELECT
              a.id,
              a.userid AS owner,
              UNIX_TIMESTAMP(a.created) AS created,
              a.lat,
              a.lng,
              a.karma,
              a.alignment,
              a.transport,
              a.text,
              b.name,
              IFNULL(a.yandex_id, "") AS yandex_id
          FROM
              mototimes_events a, mototimes_users b
          WHERE
              UNIX_TIMESTAMP() - UNIX_TIMESTAMP(a.created) < 14400 + a.karma*60
              AND a.userid = b.id_vk
              AND a.transport IN (1,2,3)
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
                    'owner' => $row['owner'],
                    'created' => $row['created'],
                    'lat' => $row['lat'],
                    'lng' => $row['lng'],
                    'karma' => $row['karma'],
                    'alignment' => $row['alignment'],
                    'transport' => $row['transport'],
                    'name' => $row['name'],
                    'yandex_id' => $row['yandex_id'],
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