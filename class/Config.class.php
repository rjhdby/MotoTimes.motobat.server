<?php

class Config
{
    const CONFIG = "config/config.php";
    private static $params;
    private static $comments;

    public static function get($key)
    {
        if (!isset(self::$params)) {
            self::readConfig();
        }
        if (isset(self::$params[ $key ])) {
            return self::$params[ $key ];
        }

        return "";
    }

    public static function getByPrefix($prefix)
    {
        if (!isset(self::$params)) {
            self::readConfig();
        }
        $result = array();
        foreach (self::$params as $key => $value) {
            if (strpos($key, $prefix) === (int)0) {
                $result[ $key ] = $value;
            }
        }

        return $result;
    }

    public static function getComment($key)
    {
        if (!isset(self::$comments)) {
            self::readConfig();
        }
        if (isset(self::$comments[ $key ])) {
            return self::$comments[ $key ];
        }

        return "";
    }

    public static function getCommentsByPrefix($prefix)
    {
        if (!isset(self::$comments)) {
            self::readConfig();
        }
        $result = array();
        foreach (self::$comments as $key => $value) {
            if (strpos($key, $prefix) === (int)0) {
                $result[ $key ] = $value;
            }
        }

        return $result;
    }

    private static function readConfig()
    {
        $content = preg_grep("/.*=.*/", file(self::CONFIG));
        foreach ($content as $row) {
            if ($row[0] == '#') continue;
            list($key, $right) = explode("=", $row, 2);
            list($value, $comment) = explode("#", $right . "#", 2);
            $key                    = trim($key);
            $value                  = trim($value);
            $comment                = trim(trim($comment), '#');
            self::$params[ $key ]   = $value;
            self::$comments[ $key ] = $comment;
        }
    }
}