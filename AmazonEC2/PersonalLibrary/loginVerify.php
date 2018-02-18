<?php
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
        
        $loginCheckQuery = "SELECT * FROM loginInfo WHERE email = '".$userEmail."', password = '".$userPass."';";
        
        $loginCheckResponse = $conn->query($loginCheckQuery);
        
        if(mysql_num_rows($loginCheckResponse)>0){
            $status = array("success");   
            return json_encode($status);
        }else{
            $status = array("failure");
            return json_encode($status);
        }
        
    }
?>