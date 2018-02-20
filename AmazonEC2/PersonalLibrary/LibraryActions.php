<?php
    try{
        require_once("Libraries.php");
        session_start();
    
        if(isset($_REQUEST['pageAction'])) {
            $pageAction = strtolower($_REQUEST['pageAction']);
        } else {
            throw new Exception("Missing required request parameter: pageAction");
        }
        
        $library = new Library();
        
        switch($pageAction) {
            case "createbook":
                $output = array($library->addbook($_REQUEST['title'], $_REQUEST['authorfirst'], $_REQUEST['authorlast']));
            break;
            
            case "deletebook":
            
            break;
            
            case "getbooks":
                $start = $_REQUEST['start'];
                $length = $_REQUEST['length'];
                $output = array($library->getbooks($start, $length));
            break;
            
            default:
                $output = array("Something went wrong! Please try again later.");
        }
    } catch(Exception $ex) {
        $output = array("error" => $ex->getMessage());
    } finally {
        header('Content-Type: application/json');
        return json_encode($output);
    }
?>