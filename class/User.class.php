<?php

class User
    extends Core
{
    private $id;
    private $name;
    private $role;
    private $karma;
    /** @var  DateTime $last_create */
    private $last_create;

    function __construct($data)
    {
        parent::__construct($data);
        $this->setPrerequisites('userid');
        $this->checkPrerequisites();
        if (!$this->isError()) {
            $this->id = $this->get('userid');
            $this->readUser();
        }
        $this->updateName();
    }

    private function updateName()
    {
        if($this->get("name") !== false && $this->id != 77){
            $this->name = $this->get("name");
            $db    = new DB();
            $query = 'UPDATE mototimes_users SET name=? WHERE id_vk=?';
            $stmt  = $db->prepare($query);
            $stmt->bind_param('si', $this->name, $this->id);
            $stmt->execute();
            $db->close();
        }
    }

    private function readUser()
    {
        $db    = new DB();
        $query = 'SELECT name, role, karma, last_create FROM mototimes_users WHERE id_vk=?';
        $stmt  = $db->prepare($query);
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $this->name = $this->get('name');
            if ($this->name === false) {
                $this->name = 'unknown';
            }
            $this->newUser();
            $this->role        = 'standart';
            $this->karma       = 0;
            $this->last_create = new DateTime();
        } else {
            $data = $result->fetch_assoc();

            $this->name        = $data['name'];
            $this->role        = $data['role'];
            $this->karma       = $data['karma'];
            $this->last_create = $data['last_create'];
            $this->last_create = new DateTime($this->last_create);
        }
        $db->close();
    }

    public function setRole()
    {
        $this->setPrerequisites('userid,newrole,targetid');
        if (!$this->checkPrerequisites()) return false;
        if (!$this->isModerator()) {
            $this->setUpError('NO RIGHTS', $this->getRole());

            return false;
        }
        $target = new User(array('userid' => $this->get('targetid')));

        if ($this->get('newrole') == $target->getRole()) {
            $this->setUpError("ALREADY IN ROLE", $this->get('newrole'));

            return false;
        };

        $db    = new DB();
        $query = 'UPDATE mototimes_users SET role=? WHERE id_vk=?';
        $stmt  = $db->prepare($query);
        $stmt->bind_param('si', $this->get('newrole'), $this->get('targetid'));
        $stmt->execute();
        if ($stmt->errno != 0) {
            $this->setUpError("NO SUCH ROLE", $this->get('newrole'));
        } else if ($stmt->affected_rows == 0) {
            $this->setUpError("NO USER", $this->get('targetid'));
        } else {
            $result = array(
                'response' => 'ok'
            );
            $this->setResult($result);
        }
        $db->close();

        return true;
    }

    public function readRole()
    {
        $result = array(
            'role' => $this->role
        );
        $this->setResult($result);
    }

    private function newUser()
    {
        $db    = new DB();
        $query = /** @lang MySQL */
            'INSERT INTO mototimes_users (id_vk, name, role) VALUES(?,?,"standart")';
        $stmt  = $db->prepare($query);
        $stmt->bind_param('is', $this->id, $this->name);
        $stmt->execute();
        $db->close();
    }

    public function getRole()
    {
        return $this->role;
    }

    public function isId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getKarma()
    {
        return $this->karma;
    }

    public function getLastCreate()
    {
        return $this->last_create;
    }

    public function isReadOnly()
    {
        return ($this->role == 'readonly');
    }

    public function isModerator()
    {
        return ($this->role == 'moderator' || $this->role == 'administrator');
    }

    public function idStandard()
    {
        return ($this->role == 'standart' || $this->role == 'moderator' || $this->role == 'administrator');
    }
}