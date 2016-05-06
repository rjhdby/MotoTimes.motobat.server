<?php

class CreateYandexPoint
    extends Core
{
    function __construct($data)
    {
        parent::__construct($data);
        $this->setPrerequisites('lat,lng,yandex_id,text');
        if ($this->checkPrerequisites()) {
            $this->addPoint();
        }
    }

    private function addPoint()
    {
        if ($this->isError()) return false;

        $db     = new DB();
        $userId = 78;
        if ($this->checkIsExists()) {
            $query = 'UPDATE mototimes_events SET created=CURRENT_TIMESTAMP WHERE yandex_id=?';
            $stmt  = $db->prepare($query);
            $stmt->bind_param('s', $this->get('yandex_id'));
        } else {
            $query = 'INSERT INTO mototimes_events (userid, lat, lng, text, yandex_id) VALUES(?,?,?,?,?)';
            $stmt  = $db->prepare($query);
            $stmt->bind_param('iddss', $userId, $this->get('lat'), $this->get('lng'),
                $this->get('text'), $this->get('yandex_id'));
        }
        $stmt->execute();
        if ($stmt->errno != 0) {
            $this->unknownError();
        } else {
            $result = array(
                'response' => 'ok'
            );
            $this->setResult($result);
        }

        return true;
    }

    private function checkIsExists()
    {
        $db    = new DB();
        $query = 'SELECT COUNT(*) FROM mototimes_events WHERE yandex_id=?';
        $stmt  = $db->prepare($query);
        $stmt->bind_param('s', $this->get('yandex_id'));
        $stmt->execute();
        $result = $stmt->get_result();

        return implode('', $result->fetch_assoc()) != 0;
    }

    private function alreadyExists()
    {
        $this->setError();
        $this->setErrorText("ALREADY EXISTS");
        $this->setErrorObject('');
    }

    private function unknownError()
    {
        $this->setError();
        $this->setErrorText("UNKNOWN ERROR");
        $this->setErrorObject('void');
    }

}