<?php
session_start();
if(isset($_REQUEST['email'])&&isset($_REQUEST['password'])){
    $servername = "localhost";
    $username = "root";
    $password = "skull71";
    
    $userEmail = $_REQUEST['email'];
    //Key should be argonn
    $userPass = crypt($_REQUEST['password'], "argonn");
    
    $conn = new mysqli($servername, $username, $password);
    
    $sql = "USE library";
    
    $conn->query($sql);
    
    $loginCheckQuery = "INSERT INTO loginInfo(emailAddress, password) 
                        VALUES('".$userEmail."', '".$userPass."');";
    
    $loginCheckResponse = $conn->query($loginCheckQuery);
    
    if(mysqli_num_rows($loginCheckResponse)>0){
        $status = array("success");
        echo json_encode($status);
    }else{
        $status = array("failure");
        echo json_encode($status);
    }
    
}
?>