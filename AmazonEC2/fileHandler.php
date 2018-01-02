<?php

$servername = "localhost";
$username = "root";
$password = "skull71";

function processUploadedFile()
{
   
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
        $updateCnt = -1;
        if(isset($_REQUEST['updateCnt']) && $_REQUEST['updateCnt'] > 0) {
            $updateCnt = $_REQUEST['updateCnt'];
        }
        
        while(($lineOfData = fgetcsv($fileForPlugin, 2048, "\t")) !== false) {
            if($cnt!=0){          // Count goes by one; this line will skip the definition line in the file
                if($updateCnt !== -1 && $cnt > $updateCnt) {
                    debugLog("Max Updates Hit.  Exiting.");
                    exit;
                }
                
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

function geoCodeAddress($addressStr)
{
    $url = "http://maps.google.com/maps/api/geocode/json?address=".urlencode($addressStr)."&sensor=false&region=US";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response);
    
    if(isset($response_a->results[0]->geometry->location)) {
        $lat = $response_a->results[0]->geometry->location->lat;
        $lng = $response_a->results[0]->geometry->location->lng;
        return array($lng, $lat);
    } else throw new Exception("Unable to GEO code address (".$addressStr.")");
}

function debugLog($str) {
    if(isset($_REQUEST['debug']) && $_REQUEST['debug'] == "1") {
        echo $str."<br>\n";
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

$userLocation = $_REQUEST['userLocation'];
$userSearch = $_REQUEST['userDistOut'];

if(isset($_REQUEST['userLocation'])){
                    
    $userCoords =  geoCodeAddress($userLocation);
    echo "Longitude of user is: ". $userCoords[0];
    echo "<br>";
    echo "Latitude of user is: ". $userCoords[1];
    echo "<br>";
}

$connSearch = new mysqli($servername, $username, $password);

if ($connSearch->connect_error){
    die("Connection failed: " . $connSearch->connect_error);
}

if(isset($userSearch)){
    $connSearch->query("USE cityInfoDB;");
    $connSearch->query("set @orig_lat=".$userCoords[1]."; set @orig_lon=".$userCoords[0]."; set @dist=".$userSearch.";");
    $searchOut = $connSearch->query("SELECT *, ( 3959 * acos( cos( radians(@orig_lon) ) * cos( radians(cityInfo.latitude) ) * cos( radians(cityInfo.longitude) - radians(@orig_lat) ) + sin( radians(@orig_lon) ) * sin(radians(cityInfo.latitude)) ) ) AS distance FROM cityInfo HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;");
                    
   
   //SELECT *, ( 3959 * acos( cos( radians(37) ) * cos( radians( cityInfo.latitude ) )  * cos( radians(cityInfo.longitude) - radians(-122) ) + sin( radians(37) ) * sin(radians(cityInfo.latitude)) ) ) AS distance  FROM cityInfo  HAVING distance < 25  ORDER BY distance  LIMIT 0 , 20;
   
    $rowCnt = $searchOut->num_rows;
    
    echo $rowCnt;
    
}
debugLog("Test: We've reached the end of this program!!!"); //Signals end of program
?>


<html>
	<body>
		<br>
		<form action=""<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="debug" value="1">
			Your Location: <input type="text" name="userLocation">
			<br>
			Distance to Search Out From: <input type="text" name="userDistOut">
			<br><br>
			------- Debug/File Update Section -------
			<br>
			Input File: 
			<input type="file" name="uploadedFile" id="uploadedFile">
			<br>
			Max Inserts: <input type="text" value="10" name="updateCnt">
			<br>
			Debug: 
			<select type="select" name="debug">
				<option value="1">Yes</option>
				<option value="0" selected>No</option>
			</select>
			<br>
			-----------------------------------------
			<br><br>
			<input type ="submit" name="submitStatus" value="Submit Request">
		</form>
	</body>
</html>