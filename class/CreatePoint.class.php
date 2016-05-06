<?php

class CreatePoint
    extends Core
{
    private static $OPEN_GROUP           = 68397238;
    private static $CLOSED_GROUP         = 98656839;
    private static $OPEN_GROUP_TIMEOUT   = 600;
    private static $CLOSED_GROUP_TIMEOUT = 10;

    function __construct($data)
    {
        parent::__construct($data);
        $this->setPrerequisites('userid,lat,lng,alignment,transport');
        if ($this->checkPrerequisites()) {
            $this->addPoint();
        }
    }

    private function addPoint()
    {
        $user = new User($this->getData());
        if ($this->get("group_id") == self::$CLOSED_GROUP) {
            $timeout = self::$CLOSED_GROUP_TIMEOUT;
        } else {
            $timeout = self::$OPEN_GROUP_TIMEOUT;
        }
        if ($user->isReadOnly()) {
            $this->noRightsError();
        } else if ((time() - $user->getLastCreate()->getTimeStamp() < $timeout) && !$user->isModerator()) {
            $this->timeOutError(round((time() - $user->getLastCreate()->getTimeStamp()) / 60));
        } else {
            $userId = $this->get('userid');
            $db     = new DB();

            $query = /** @lang MySQL */
                'INSERT INTO mototimes_events (userid, lat, lng, alignment, transport, text) VALUES(?,?,?,?,?,?)';
            $stmt  = $db->prepare($query);
            $stmt->bind_param('iddiis', $userId, $this->get('lat'), $this->get('lng'), $this->get('alignment'), $this->get('transport'), $this->get('text'));

            $stmt->execute();
            if ($stmt->errno != 0) {
                $this->unknownError();
            } else {
                $query = 'UPDATE mototimes_users SET last_create = NOW() WHERE id_vk=?';
                $stmt  = $db->prepare($query);
                $stmt->bind_param('i', $userId);
                $stmt->execute();
                $result = array(
                    'response' => 'ok'
                );
                $this->setResult($result);
            }
        }
    }

    private function noRightsError()
    {
        $this->setError();
        $this->setErrorText("NO RIGHTS");
        $this->setErrorObject('readonly');
    }

    private function unknownError()
    {
        $this->setError();
        $this->setErrorText("UNKNOWN ERROR");
        $this->setErrorObject('void');
    }

    private function timeOutError($minutes)
    {
        $this->setError();
        $this->setErrorText("TIMEOUT");
        $this->setErrorObject(10 - $minutes);
    }
}
