<?php
    require_once("BaseDBWrapper.php");

    class Library extends BaseDBWrapper{

        public function addbook($title, $authorfirst, $authorlast, $isbn){
            if($isbn!=""){
                $sql = $this->escapeString($isbn);
            } else {
                $sql = "NULL";
            }
            
            return $this->runQuery("INSERT INTO books(title, authorfirst, authorlast, bookownerid, isbn) VALUES('".$this->escapeString($title)."','".$this->escapeString($authorfirst).
                                    "','".$this->escapeString($authorlast)."',".$_SESSION['USER_ID'].", ".$sql.");");
        }
        
        public function removebook($title, $authorlast){
            if(sizeof($this->getQueryResults("SELECT * FROM books WHERE title='".$title."' AND authorlast='".$authorlast."';"))>0){
                return($this->runQuery("DELETE FROM books WHERE title='".$title."' AND authorlast='".$authorlast."';"));
            }else{
                return(array("error" => "nonexistant"));
            }
        }
        
        public function getbooks($start, $length){
            $resultcountuntrimmed = sizeof($this->getQueryResults("SELECT * FROM books WHERE bookownerid = ".$_SESSION['USER_ID']));
            $results = $this->getQueryResultsLimited("SELECT * FROM books WHERE bookownerid = ".$_SESSION['USER_ID'], $start, $length);
            
            return array("data" => $results, "recordsTotal" => $resultcountuntrimmed, "recordsFiltered" => $resultcountuntrimmed);;
        }
        
        public function getbookbyname(String $name){
            
        }
    }
?>