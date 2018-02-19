<?php

require_once("BaseDBWrapper.php");

class User extends BaseDBWrapper
{
    private function encryptPassword($password)
    {
        return crypt($password, "argonn");
    }
    
    public function addUser($emailAddress, $password)
    {
        $result = $this->runQuery("INSERT INTO loginInfo(emailAddress, password) VALUES('".$this->escapeString($emailAddress)."', '".$this->encryptPassword($password)."');");
        return $this->getLastInsertId();
    }
    
    public function deleteUser($userId)
    {
        return $this->runQuery("DELETE FROM loginInfo where ID = '".$this->escapeString($userId)."'");
    }
    
    public function getUser($userId)
    {
        $data = $this->getQueryResults("select * from loginInfo where ID = '".$this->escapeString($userId)."' order by ID ASC");
        if($data > 0) {
            return $data[0];
        } else return null;
    }
    
    public function updateUserPassword($emailAddress, $password)
    {
        return $this->runQuery("update loginInfo set password='".$this->encryptPassword($password)."' where emailAddress='".$this->escapeString($emailAddress)."'");
    }
    
    public function getUserWithPassword($emailAddress, $password)
    {
        $data = $this->getQueryResults("select * from loginInfo where emailAddress = '".$this->escapeString($emailAddress)."' and password = '".$this->encryptPassword($password)."'");
        if(count($data) > 0) {
            return $data[0];
        } else return null;
    }
    
}