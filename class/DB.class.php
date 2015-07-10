<?php

class DB
    extends mysqli
{
    const HOST = 'db.host';
    const LOGIN = 'db.login';
    const PASS = 'db.password';
    const DB = 'db.db';

    function __construct()
    {
        parent::__construct(
            Config::get(self::HOST),
            Config::get(self::LOGIN),
            Config::get(self::PASS),
            Config::get(self::DB));
        parent::set_charset("utf8");
    }

    function insert($tablename, $values)
    {
        $this->query('
				INSERT INTO ' . $tablename . '
				(' . implode(',', array_keys($values)) . ')
				VALUES
				(' . $this->makeDBDataset($values) . ')
				;');
    }

    private function makeDBDataset($arr)
    {
        $result = '';
        foreach ($arr as $value) {
            if (is_int($value)) {
                $result .= $value . ',';
            } else {
                $result .= '"' . str_replace("\\n", "\n", $this->real_escape_string($value)) . '",';
            }
        }

        return trim($result, ',');
    }
}