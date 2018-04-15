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
                if($_REQUEST['isbn']==""){
                    $output = array("data" => $library->addbook($_REQUEST['title'], $_REQUEST['authorfirst'], $_REQUEST['authorlast'], $_REQUEST['isbn']));
                } else if(strlen($_REQUEST['isbn'])==13&&ctype_digit($_REQUEST['isbn'])){
                    $output = array("data" => $library->addbook($_REQUEST['title'], $_REQUEST['authorfirst'], $_REQUEST['authorlast'], $_REQUEST['isbn']));
                } else if(strlen($_REQUEST['isbn'])==10&&ctype_digit($_REQUEST['isbn'])){
                    $output = array("data" => $library->addbook($_REQUEST['title'], $_REQUEST['authorfirst'], $_REQUEST['authorlast'], $_REQUEST['isbn']));
                } else {
                    $output = array("error" => "invalidisbn");
                }
            break;
            
            case "deletebook":
                $output = array("data" => $library->removebook($_REQUEST['title'], $_REQUEST['authorlast']));
            break;
            
            case "editbook":
                $output = array("data" => $library->editbook($_REQUEST['bookID'], $_REQUEST['title'], 
                                $_REQUEST['authorfirst'], $_REQUEST['authorlast'], $_REQUEST['isbn']));
            break;
            
            case "getbooks":
                $start = (isset($_REQUEST['start']) ? $_REQUEST['start'] : null);
                $length = (isset($_REQUEST['length']) ? $_REQUEST['length'] : null);
                
                $title = (isset($_REQUEST['title']) ? $_REQUEST['title'] : null);
                $authorfirst = (isset($_REQUEST['authorfirst']) ? $_REQUEST['authorfirst'] : null);
                $authorlast = (isset($_REQUEST['authorlast']) ? $_REQUEST['authorlast'] : null);
                $isbn = (isset($_REQUEST['isbn']) ? $_REQUEST['isbn'] : null);
                              
                $rowCount = 0;
                $results = $library->getbooks(
                    $title, 
                    $authorfirst,
                    $authorlast,
                    $isbn,
                    $start,
                    $length,
                    $rowCount
                );
                
                //Draw for datatables
                $draw = (isset($_REQUEST['draw']) ? $_REQUEST['draw'] : ""); 
                
                //Make the data friendly for data tables
                $output = array(
                    "data" => $results,
                    "recordsTotal" => count($results),
                    "recordsFiltered" => $rowCount,
                    "draw" => $draw
                );
                
            break;
            
            default:
                throw new Exception("Unsupported action (".$pageAction.")");
        }
    } catch(Exception $ex) {
        $output = array("error" => $ex->getMessage());
    } finally {
        header('Content-Type: application/json');
        echo json_encode($output);
    }
?>