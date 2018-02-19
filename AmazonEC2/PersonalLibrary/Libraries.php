<?php
    require_once("BaseDBWrapper.php");

    class Library extends BaseDBWrapper{

        public function addbook($title, $authorfirst, $authorlast){
            return $this->getQueryResults("INSERT INTO books(title, authorfirst, authorlast, bookownerid) VALUES('".$title."','".$authorfirst."','".$authorlast."',".$_SESSION['USER_ID'].");");
        }
        
        public function removebook(String $title, String $authorlast){
            
        }
        
        public function getbooks(int $start, int $length){
            
        }
        
        public function getbookbyname(String $name){
            
        }
    }
?>