<?php
    require_once("BaseDBWrapper.php");

    class Library extends BaseDBWrapper{

        public function addbook($title, $authorfirst, $authorlast){
            
            return $this->runQuery("INSERT INTO books(title, authorfirst, authorlast, bookownerid) VALUES('".$this->escapeString($title)."','".$this->escapeString($authorfirst)."','".$this->escapeString($authorlast)."',".$_SESSION['USER_ID'].");");
        }
        
        public function removebook(String $title, String $authorlast){
            
        }
        
        public function getbooks($start, $length){
            $resultcountuntrimmed = sizeof($this->getQueryResults("SELECT * FROM books WHERE bookownerid = ".$_SESSION['USER_ID']));
            $results = $this->getQueryResultsLimited("SELECT * FROM books WHERE bookownerid = ".$_SESSION['USER_ID'], $start, $length);
            
            $data = array("data" => $results, "recordsTotal" => $resultcountuntrimmed, "recordsFiltered" => $resultcountuntrimmed);
            
            return $data;
        }
        
        public function getbookbyname(String $name){
            
        }
    }
?>