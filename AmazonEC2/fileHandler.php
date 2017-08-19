<html>
	<body>
			Input File:
		<form action="fileHandler.php" method="post" enctype="multipart/form-data">
			<br>
			<input type="file" name="uploadedFile" id="uploadedFile">
			<br>
			<input type ="submit" name="submitStatus" value="Submit">
		</form>
	</body>
</html>

<?php

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["uploadedFile"]["name"]);
$uploadOk = 1;
$textFileType = pathinfo($target_file,PATHINFO_EXTENSION);

//Does the file exist? ----------------------
if($_FILES["uploadedFile"]["size"] > 0){

//Is it a valid file type? -------------------
    if($textFileType != "docx" && $textFileType != "doc" && $textFileType != "txt"){
        echo "File Type Is Invalid - Valid Types Are: .docx, .doc, or .txt";
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
    
    //work on later
    if($conn->query("LOAD DATA LOCAL INFILE ".$_FILES["uploadedFile"]['tmp_name']."
     INTO TABLE cityInfo
    FIELDS
    TERMINATED BY '\t'
            OPTIONALLY ENCLOSED BY '\"'
            (city,state,population,latitude,longitude)
    ")){echo("File Successfully Processed");}else{echo("Error:" . $conn->error );};
} 




?>