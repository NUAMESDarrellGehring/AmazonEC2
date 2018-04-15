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
        $this->conn = new mysqli($this->servername, $this->username, $this->password, 'library');
    }
    
    public function __destruct()
    {
        try { $this->conn->close(); } catch(Exception $ex) { }
    }
    
    public function getQueryResults($sql, $start = null, $length = null){
        if($start === null || $start < 0) {
            $start = null;
        }
        if($length === null || $length <= 0) {
            $length = null; //Default to null if invalid or not giver
        }
        
        if($start !== null && $length !== null) {
            $sql .= " LIMIT ".$start.", ".$length;
        }
        
        $results = $this->conn->query($sql);
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
        $results = $this->conn->query($sql);
        if($results === false) {
            //Error!
            throw new Exception("Query Failed (".$sql."): ".mysqli_error($this->conn));
        } else {
            return $results;
        }
    }
    
    public function getLastInsertId()
    {
        return $this->conn->insert_id;
    }
    
    public function escapeString($str)
    {
        return $this->conn->real_escape_string($str);
    }
    
}
?>