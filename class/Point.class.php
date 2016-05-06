<?php

class Point
    extends Core
{
    private $id;
    private $alignment;
    private $created;
    private $owner;
    private $karma;
    private $lat;
    private $lng;
    private $transport;

    function __construct($data)
    {
        parent::__construct($data);
        $this->setPrerequisites('userid,id');
        $this->checkPrerequisites();
        if ($this->isError()) return;
        $this->id = $this->get('id');
        $this->readPoint();
    }

    private function readPoint()
    {
        $db    = new DB();
        $query = 'SELECT userid, created, lat, lng, karma, alignment, transport FROM mototimes_events WHERE id=?';
        $stmt  = $db->prepare($query);
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $this->setUpError("NO POINT", $this->id);
        } else {
            $data            = $result->fetch_assoc();
            $this->alignment = $data['alignment'];
            $this->transport = $data['transport'];
            $this->lat       = $data['lat'];
            $this->lng       = $data['lng'];
            $this->karma     = $data['karma'];
            $this->owner     = $data['userid'];
            $this->created   = $data['created'];
        }
        $db->close();
    }

    public function changeKarma()
    {
        if ($this->isError()) return false;
        $this->setPrerequisites('karma');
        $this->checkPrerequisites();
        if ($this->isError()) return false;
        $user = new User($this->getData());
        if (!$user->isModerator()) {
            $this->setUpError("NO RIGHTS");

            return false;
        }
        $karma = $this->karma + $this->get('karma');
        $db    = new DB();
        $query = 'UPDATE mototimes_events SET karma=? WHERE id=?';
        $stmt  = $db->prepare($query);
        $stmt->bind_param('ii', $karma, $this->id);
        $stmt->execute();
        if ($stmt->errno) {
            $this->setUpError();
        } else {
            $result = array(
                'response' => 'ok'
            );
            $this->setResult($result);
        }

        return true;
    }
}