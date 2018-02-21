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
                $output = array("data" => $library->addbook($_REQUEST['title'], $_REQUEST['authorfirst'], $_REQUEST['authorlast'], $_REQUEST['isbn']));
            break;
            
            case "deletebook":
                $output = array("data" => $library->removebook($_REQUEST['title'], $_REQUEST['authorlast']));
            break;
            
            case "getbooks":
                $start = $_REQUEST['start'];
                $length = $_REQUEST['length'];
                $output = $library->getbooks($start, $length);
            break;
            
            default:
                $output = array("Something went wrong! Please try again later.");
        }
    } catch(Exception $ex) {
        $output = array("error" => $ex->getMessage());
    } finally {
        header('Content-Type: application/json');
        echo json_encode($output);
    }
?>