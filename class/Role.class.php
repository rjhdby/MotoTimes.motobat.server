<?php

class Role
    extends Core
{
    function __construct($data)
    {
        parent::__construct($data);
        $this->setPrerequisites('userid');
        $this->checkPrerequisites() && $this->setId($this->get('userid'));
    }

    public function getRole()
    {
        $db    = new DB();
        $query = /** @lang MySQL */
            'SELECT role FROM mototimes_users WHERE id_vk=?';
        $stmt  = $db->prepare($query);
        $stmt->bind_param('s', $this->get('userid'));
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $this->noUserError();
        } else {
            $result = array(
                'role' => implode($result->fetch_row())
            );
            $this->setResult($result);
        }
    }

    public function setRole()
    {
        $this->setPrerequisites('userid,role');
        if (!$this->checkPrerequisites()) return false;
        $this->getRole();
        $current = $this->getResult();
        $current = $current['role'];

        if ($this->get('role') == $current) {
            $this->alredyInRoleError();

            return false;
        };

        $db    = new DB();
        $query = /** @lang MySQL */
            'UPDATE mototimes_users SET role=? WHERE id_vk=?';
        $stmt  = $db->prepare($query);
        $stmt->bind_param('ss', $this->get('role'), $this->get('userid'));
        $stmt->execute();
        if ($stmt->affected_rows == 0) {
            $this->noUserError();
        } else {
            $result = array(
                'response' => 'ok'
            );
            $this->setResult($result);
        }

        return true;
    }

    private function setId($id)
    {
        $this->id = $id;
    }

    private function noUserError()
    {
        $this->setError();
        $this->setErrorText("NO USER");
        $this->setErrorObject($this->get('userid'));
    }

    private function alredyInRoleError()
    {
        $this->setError();
        $this->setErrorText("ALREADY IN ROLE");
        $this->setErrorObject($this->get('role'));
    }
}