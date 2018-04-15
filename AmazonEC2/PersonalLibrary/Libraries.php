<?php
    require_once("BaseDBWrapper.php");

    class Library extends BaseDBWrapper{

        public function addbook($title, $authorfirst, $authorlast, $isbn){
            
            if($title === null || trim($title) === "") {
                throw new Exception("Title is a required");
            }
            if($authorfirst === null || trim($authorfirst) === "") {
                throw new Exception("Authors First Name is a required");
            }
            if($authorlast === null || trim($authorlast) === "") {
                throw new Exception("Authors Last Name is a required");
            }
            
            $existingBooks = $this->getbooks($title, $authorfirst, $authorlast, $isbn);
            if(count($existingBooks) > 0) {
                throw new Exception("Another book already exists with these properties");
            }
            
            if($isbn!=""){
                $sql = $this->escapeString($isbn);
            } else {
                $sql = "";
            }
            
            return $this->runQuery("INSERT INTO books(title, authorfirst, authorlast, bookownerid, isbn) VALUES('".$this->escapeString($title)."','".$this->escapeString($authorfirst).
                                    "','".$this->escapeString($authorlast)."',".$_SESSION['USER_ID'].", ".$sql.");");
        }
        
        public function removebook($bookID){
            if(sizeof($this->getQueryResults("SELECT * FROM books WHERE id='".$bookID."';"))>0){
                return($this->runQuery("DELETE FROM books WHERE id='".$bookID."';"));
            }else{
                return(array("error" => "nonexistant"));
            }
        }
        
        public function editbook($bookID, $title, $authorfirst, $authorlast, $isbn){
            
            if($title === null || trim($title) === "") {
                throw new Exception("Title is a required");
            }
            if($authorfirst === null || trim($authorfirst) === "") {
                throw new Exception("Authors First Name is a required");
            }
            if($authorlast === null || trim($authorlast) === "") {
                throw new Exception("Authors Last Name is a required");
            }
            
            $referenceIntOf0 = 0;
            $existingBooks = $this->getbooks($title, $authorfirst, $authorlast, $isbn, null, null, $referenceIntOf0);
            if(count($existingBooks) > 0) {
                throw new Exception("Another book already exists with these properties (".var_export($existingBooks, true).")");
            }
            
            if($isbn!=""){
                $sql = $this->escapeString($isbn);
            } else {
                $sql = "";
            }
            
            return $this->runQuery("UPDATE books SET title='".$this->escapeString($title)
                ."', authorfirst='".$this->escapeString($authorfirst)."', authorlast='".$this->escapeString($authorlast)."', isbn='".$sql."'"
                ." WHERE id=".$bookID.";");
        }
        
        /*
        public function getbooks($start, $length){
            $resultcountuntrimmed = sizeof($this->getQueryResults("SELECT * FROM books WHERE bookownerid = ".$_SESSION['USER_ID']));
            $results = $this->getQueryResults("SELECT * FROM books WHERE bookownerid = ".$_SESSION['USER_ID'], $start, $length);
            
            return array("data" => $results, "recordsTotal" => $resultcountuntrimmed, "recordsFiltered" => $resultcountuntrimmed);;
        }
        */
        
        public function getbooks(
            $title,
            $authorfirst, 
            $authorlast, 
            $isbn, 
            $start, 
            $length,
            &$rowCount
        ) {
            
            $sql = "
                SELECT 
                    SQL_CALC_FOUND_ROWS
                    *
                FROM books
                WHERE ";
            $cnt = 0;
            if($title != null && $title != "") {
                $sql .= " title like '".$this->escapeString(trim($title))."'";
                $cnt++;
            }
            if($authorfirst != null && $authorfirst != "") {
                if($cnt > 0) $sql .= " AND ";
                $sql .= " authorfirst like '".$this->escapeString(trim($authorfirst))."'";
                $cnt++;
            }
            if($authorlast != null && $authorlast != "") {
                if($cnt > 0) $sql .= " AND ";
                $sql .= " authorfirst like '".$this->escapeString(trim($authorlast))."'";
                $cnt++;
            }
            if($isbn != null && $isbn != "") {
                if($cnt > 0) $sql .= " AND ";
                $sql .= " isbn like '".$this->escapeString(trim($isbn))."'";
                $cnt++;
            }
            if($cnt > 0) $sql .= " AND ";
            $sql .= " bookownerid=".$_SESSION['USER_ID'];

            $results = $this->getQueryResults($sql, $start, $length);
            $rowCount = $this->getQueryResults("SELECT FOUND_ROWS() as cnt;")[0]['cnt'];
            /*return array(
                "data" => $results, 
                "recordsTotal" => $rowCount, 
                "recordsFiltered" => count($results)
            );*/
            
            if(isset($_REQUEST['debug'])) {
                echo "Query='".$sql.".\nData:'".var_export($results, true)."'\nCount:".$rowCount."\n";
                exit;
            }
            
            return $results;
            
        }
    }
?>