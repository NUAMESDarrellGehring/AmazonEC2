<?php
    require_once("Libraries.php");
    session_start();

    if(isset($_REQUEST['pageAction'])) {
        $pageAction = strtolower($_REQUEST['pageAction']);
    } else {
        throw new Exception("Missing required request parameter: pageAction");
    }
    
    $library = new Library();
    
    switch($pageAction) {
        case "createBook":
            $library->addbook($_REQUEST['title'], $_REQUEST['authorfirst'], $_REQUEST['authorlast']);
        break;
        
        case "deleteBook":
        
        break;
        
        case "getBooks":
            
        break;
    }

?>