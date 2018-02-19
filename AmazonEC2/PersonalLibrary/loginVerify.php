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
        
        $loginCheckQuery = "SELECT * FROM loginInfo WHERE emailAddress = '".$userEmail."' AND password = '".$userPass."';";
        
        $loginCheckResponse = $conn->query($loginCheckQuery);
        
        if(mysqli_num_rows($loginCheckResponse)>0){
            $status = array();
            while($row = mysqli_fetch_assoc($loginCheckResponse)) {
                $status[] = $row;
            }
            $_SESSION['USER_ID']=$status[0];
            $_SESSION['USER_EMAIL']=$status[1];
            echo json_encode($status);
        }else{
            $status = array("failure");
            echo json_encode($status);
        }
    }
?>