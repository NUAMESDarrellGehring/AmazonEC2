<?php

function debugLog($str) {
    if(isset($_REQUEST['debug']) && $_REQUEST['debug'] == "1") {
        echo $str."<br>\n";
    }
}

function processUploadedFile(){
  
    ini_set('upload_max_filesize', '1M');
    ini_set('post_max_size', '1M');
    ini_set('max_input_time', 300);
    ini_set('max_execution_time', 300);
    
    $servername = "localhost";
    $username = "root";
    $password = "skull71";
    
    $conn = new mysqli($servername, $username, $password);
    
    if ($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->query("USE cityInfoDB");
    $conn->query("DROP TABLE IF EXISTS cityInfo");
    $conn->query("CREATE TABLE cityInfo(
            city char(30) NOT NULL,
            state char(2) NOT NULL,
            population int NOT NULL,
            latitude decimal(10,8) NOT NULL,
            longitude decimal(11,8) NOT NULL
        )");
    
    $fileForPlugin = null;
    
    try {
        $fileForPlugin = @fopen($_FILES["uploadedFile"]['tmp_name'], "r");
        
        
        if($fileForPlugin === false) {
            throw new Exception("Failed to open uploaded file for reading (".$_FILES["uploadedFile"]['tmp_name'].")");
        }
        
        $cnt = 0;
        
        while(($lineOfData = fgetcsv($fileForPlugin, 2048, "\t")) !== false) {
            if($cnt!=0){          // Count goes by one; this line will skip the definition line in the file
                debugLog("Line[".($cnt + 1)."]: ".var_export($lineOfData, false));
                
                $city = $conn->real_escape_string($lineOfData[0]);
                $state = $conn->real_escape_string($lineOfData[1]);
                $population = $conn->real_escape_string($lineOfData[2]);
                $latitude = $conn->real_escape_string($lineOfData[3]);
                $longitude = $conn->real_escape_string($lineOfData[4]);
                
                $sql = "INSERT INTO cityInfo VALUES ('".$city."','".$state."','".$population."','".$latitude."','".$longitude."')";
                
                if($conn->query($sql)) {
                    debugLog("Line[".($cnt + 1)."]: ('".$sql."') Data Inserted Into DB.");
                } else {
                    throw new Exception("Query Failed (".$sql."): ".$conn->error);
                }
            }
            $cnt++;
        }
    } catch(Exception $ex) {
        throw $ex;
    } finally {
        try {
            @fclose($fileForPlugin); // Will attempt to close the file for plugin
        } catch(Exception $ex2) { } // Will catch any errors that begin when file is attemptedly closed
    }
}


$uploadOk = 1;

$fileExtension = pathinfo($_FILES["uploadedFile"]["name"],PATHINFO_EXTENSION);

debugLog("Received File: ".var_export($_FILES, true));

//Does the file exist? ----------------------
if($_FILES["uploadedFile"]["size"] !== 0){
    //Is it a valid file type? -------------------
    if($uploadOk !== 0 && $fileExtension != "txt"){
        echo "File Type Is Invalid - Valid Types Are: .txt";
        echo "<br>";
        $uploadOk = 0;
    }
    
    //Is it a valid file size? -------------------
    if($uploadOk !== 0 && $_FILES["uploadedFile"]["size"] > 1024 * 700){
        echo "File Is Too Large: Max Size Is 700 KB";
        echo "<br>";
        $uploadOk = 0;
    }
    
    //Does it check any errors? ------------------
    if($uploadOk == 0){
        echo "File Upload Failed";
        echo "<br>";
    } else {
        processUploadedFile();
    }
}

?>