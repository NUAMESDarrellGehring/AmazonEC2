<?php

/* Abstract class to deal with connecting to the db and executing simple queries */

abstract class BaseDBWrapper
{
    private $servername = "localhost";
    private $username = "root";
    private $password = "skull71";
    
    private $conn = null;
        
    public function __construct()
    {
        $conn = new mysqli($this->servername, $this->username, $this->password, 'library');
    }
    
    public function __destruct()
    {
        try { $conn->close(); } catch(Exception $ex) { }
    }
    
    public function getQueryResults($sql)
    {
        $results = $conn->query($sql);
        if($results === false) {
            //Error!
            throw new Exception("Query Failed (".$sql."): ".mysqli_error($conn));
        } else {
            $data = array();
            while($row = mysqli_fetch_assoc($results)) {
                $data[] = $row;
            }
            return $data;
        }
    }
    
    public function runQuery($sql)
    {
        $results = $conn->query($sql);
        if($results === false) {
            //Error!
            throw new Exception("Query Failed (".$sql."): ".mysqli_error($conn));
        } else {
            return $results;
        }
    }
    
    public function getLastInsertId()
    {
        return $conn->insert_id;
    }
    
    public function escapeString($str)
    {
        return $conn->real_escape_string($str);
    }
    
}
?>