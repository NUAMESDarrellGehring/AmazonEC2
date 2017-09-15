<?php

function debug($str) {
    if(isset($_REQUEST['debug']) && $_REQUEST['debug'] == "1") {
        echo $str."\n<br>";
    }
}

debug("Start Of Process: ".var_export($_FILES, true));

if(isset($_FILES["uploadedFile"]["error"])) {
    $phpFileUploadErrors = array(
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.'
    );
    
    //debug(var_export($phpFileUploadErrors, true));
    
    try {
        throw new Exception("File Upload Error: ".$phpFileUploadErrors[$_FILES["uploadedFile"]["error"]]);
    } catch(Exception $ex) {
        $tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
        
        debug("ERROR[".$_FILES["uploadedFile"]["error"]."] (".$tmp_dir.") ".$ex->getMessage());
        exit;
    }
}

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["uploadedFile"]["name"]);
$uploadOk = 1;
$textFileType = pathinfo($target_file,PATHINFO_EXTENSION);
//Does the file exist? ----------------------
if($_FILES["uploadedFile"]["size"] > 0){
    debug("Got File Size");
//Is it a valid file type? -------------------
    if($textFileType != "docx" && $textFileType != "doc" && $textFileType != "txt"){
        echo "File Type Is Invalid - Valid Types Are: .docx, .doc, or .txt";
        echo "<br>";
        $uploadOk = 0;
    }
    
    debug("Step 1 [".$uploadOk."]");
    
//Is it a valid file size? -------------------
    if($_FILES["uploadedFile"]["size"] > 1024 * 700){
        echo "File Is Too Large: Max Size Is 700 KB";
        echo "<br>";
        $uploadOk = 0;
    }
    
    debug("Step 2 [".$uploadOk."]");
    
//Does it check any errors? ------------------    
    if($uploadOk == 0){
        echo "File Upload Failed";
        echo "<br>";
    }
    
    debug("Step 3 [".$uploadOk."]");
    
    if($uploadOk !== 0) {
        
        debug("Uplpoad OK");
        
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
          
        $fileForPlugin = fopen($_FILES["uploadedFile"]['tmp_name']);
        
        $cnt = 0;
        while(($lineOfData = fgetcsv($fileFor, 2048, "\t")) !== false) {
            
           debug("Processing Line [".$i."]: ".var_export($lineOfData, false));
            
           if($cnt < 10) {
                echo "Line[".$i."]: ".var_export($lineOfData, false)."\n<br>"; 
            } else exit;
            $city = $lineOfData[0];
            $state = $lineOfData[1];
            $population = $lineOfData[2];
            $latitude = $lineOfData[3];
           $longitude = $lineOfData[4];
           echo $city;
           if($conn->query("INSERT INTO cityInfo VALUES ('".$city."','".$state."','".$population."','".$latitude."','".$longitude."');")){}
           else{echo($conn->error);}
           $cnt++;
        }
        $fclose($fileForPlugin);
       echo "Test: We've reached the end of this program!";
    }
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
		</form>
	</body>
</html>