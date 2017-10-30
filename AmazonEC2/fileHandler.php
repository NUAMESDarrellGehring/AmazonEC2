<?php

function debugLog($str) {
    if(isset($_REQUEST['debug']) && $_REQUEST['debug'] == "1") {
        echo $str."<br>\n";
    }
}

//$target_dir = "uploads/";
//$target_file = $target_dir . basename($_FILES["uploadedFile"]["name"]);
$uploadOk = 1;

//$textFileType = pathinfo($target_file,PATHINFO_EXTENSION);
$fileExtension = pathinfo($_FILES["uploadedFile"]["name"],PATHINFO_EXTENSION);

debugLog("Received File: ".var_export($_FILES, true));

//Does the file exist? ----------------------
if($_FILES["uploadedFile"]["size"] == 0){
    echo "File Does Not Exist";
    echo "<br>";
    $uploadOk = 0;
}

//Is it a valid file type? -------------------
if($fileExtension != "txt"){
    echo "File Type Is Invalid - Valid Types Are: .csv";
    echo "<br>";
    $uploadOk = 0;
}
        
//Is it a valid file size? -------------------
if($_FILES["uploadedFile"]["size"] > 1024 * 700){
    echo "File Is Too Large: Max Size Is 700 KB";
    echo "<br>";
    $uploadOk = 0;
}
        
//Does it check any errors? ------------------    
    if($uploadOk == 0){
        echo "File Upload Failed";
        echo "<br>";
    }
        
    if($uploadOk !== 0) {
                
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
            latitude decimal(10,10) NOT NULL,
            longitude decimal(10,10) NOT NULL    
        )");
          
        $fileForPlugin = null;
        try {
            $fileForPlugin = fopen($_FILES["uploadedFile"]['tmp_name']);
        
        
            if($fileForPlugin === false) {
                throw new Exception("Failed to open uploaded file for reading (".$_FILES["uploadedFile"]['tmp_name'].")");
            }
            
            $cnt = 0;
            while(($lineOfData = fgetcsv($fileForPlugin, 2048, "\t")) !== false) {
                            
                if($cnt > 10) {
                 exit;
                }
                
                echo "Line[".($cnt + 1)."]: ".var_export($lineOfData, false)."\n<br>";
                
                $city = $lineOfData[0];
                $state = $lineOfData[1];
                $population = $lineOfData[2];
                $latitude = $lineOfData[3];
               $longitude = $lineOfData[4];
               echo $city;
               if($conn->query("INSERT INTO cityInfo VALUES ('".$city."','".$state."','".$population."','".$latitude."','".$longitude."');")) {
                   
               } else {
                    echo($conn->error);
                    exit;
               }
               $cnt++;
            }
        } catch(Exception $ex) {
            throw $ex;    
        } finally {
            try {
                fclose($fileForPlugin);
            } catch(Exception $ex2) { }
        }
        
        echo "Test: We've reached the end of this program!!!";
    }
?>
<html>
	<body>
			Input File:
		<form action=""<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="debug" value="1">
			<br>
			<input type="file" name="uploadedFile" id="uploadedFile">
			<br>
			<input type ="submit" name="submitStatus" value="Submit">
			<br>
			Debug: 
			<select type="select" name="debug">
				<option value="1">Yes</option>
				<option value="0" selected>No</option>
			</select>
		</form>
	</body>
</html>