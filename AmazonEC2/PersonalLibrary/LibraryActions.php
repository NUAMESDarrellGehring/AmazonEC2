<?php
session_start();
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
                
            break;
            
            default:
                $output = array("Something went wrong!");
        }
    } catch(Exception $ex) {
        $output = array("error" => $ex->getMessage());
    } finally {
        header('Content-Type: application/json');
        echo json_encode($output);
    }
?>