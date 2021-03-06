<?php 

require_once("Users.php");

//Start the session
session_start();

$output = "NULL!!";

try {
    
    $pageAction = "";
    if(isset($_REQUEST['pageAction'])) {
        $pageAction = strtolower($_REQUEST['pageAction']);
    } else {
        throw new Exception("Missing required request parameter: pageAction");
    }

    $user = new User();
    
    switch($pageAction) {
        
        case "createuser":
            if(isset($_REQUEST['email'])&&isset($_REQUEST['password'])){
                $emailAddress = $_REQUEST['email'];
                $password = $_REQUEST['password'];
                $userId = $user->addUser($emailAddress, $password);
                $output = json_encode($user->getUser($userId));
            } else {
                throw new Exception("Missing one or more required request parameters: email, password");
            }

            break;
            
        case "userlogin":
            if(isset($_REQUEST['email'])&&isset($_REQUEST['password'])) {
                $currentUser = $user->getUserWithPassword($_REQUEST['email'], $_REQUEST['password']);
                if($currentUser != null) {
                    $_SESSION['USER_ID']=$currentUser['ID'];
                    $_SESSION['USER_EMAIL']=$currentUser['emailAddress'];
                    $output = json_encode($currentUser);
                } else {
                    throw new Exception("Invalid Username / Password");
                }
            } else {
                throw new Exception("Missing one or more required request parameters: email, password");
            }
            
            break;
        
        default:
            throw new Exception("Unsupported Action: ".$pageAction);
    }

} catch(Exception $ex) {
    $output = json_encode(array("error" => $ex->getMessage()));
} finally {
    header('Content-Type: application/json');
    echo $output;
}

?>