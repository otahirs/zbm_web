<?php
namespace Grav\Plugin\Database;

class PDO extends \PDO
{
    public  function __call($func, $args)
    {
        if(!\in_array($func, ['select', 'selectall', 'update', 'delete', 'insert'])) {
            throw new \RuntimeException($func." is not a valid statement");
        }

        if(\count($args) === 2){
            $stmt = parent::prepare($args[0]);
            $stmt->execute($args[1]);
        }else if($args){
            $stmt = parent::query($args[0]);
        }
        if((int)$stmt->errorCode()){
            throw new \RuntimeException($stmt->errorInfo()[2]);
        }
        if($func === 'select'){
            return $stmt->fetch();
        }
        if($func === 'selectall'){
            return $stmt->fetchAll();
        }
        if($func === 'insert'){
            return parent::lastInsertId();
        }

        return $stmt->rowCount();
    }

    public function tableExists($table)
    {
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = $this->query("SELECT 1 FROM $table LIMIT 1");
        } catch (\PDOException $e) {
            // We got an exception == table not found
            return FALSE;
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return $result !== FALSE;
    }
}