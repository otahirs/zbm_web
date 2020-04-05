<?php

namespace Grav\Plugin\Database;

class Database
{
    private $connections = [];

    public function connect($connect_string, $user='',$pwd='', $options=[])
    {
        if (!array_key_exists($connect_string, $this->connections)) {
            $this->connections[$connect_string] = new PDO($connect_string, $user, $pwd, $options);
        }
        return $this->connections[$connect_string];
    }
}
