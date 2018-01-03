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

function echoResults($results) {
    $arr = mysqli_fetch_array($results);
    var_export($results, true);
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

if(isset($userSearch)) {
    
    $sql = "USE cityInfoDB;";
            
    $connSearch->query($sql);
    
    debugLog("<br>Query1: ".$sql."\n<br>");
    
    $sql = "
        SELECT 
        	*,
        	(3963.17 * ACOS(COS(RADIANS(latpoint)) 
                 * COS(RADIANS(latitude)) 
                 * COS(RADIANS(longpoint) - RADIANS(longitude)) 
                 + SIN(RADIANS(latpoint)) 
                 * SIN(RADIANS(latitude)))) AS distance_in_miles
         FROM cityInfo
         JOIN (
             SELECT  ".$userCoords[1]." AS latpoint, ".$userCoords[0]."AS longpoint
        ) AS p ON 1=1
        HAVING
            distance_in_miles <= ".$userSearch."
        ORDER BY 
        	distance_in_miles
        LIMIT 15;";
       
    $results = $connSearch->query($sql);
    if($results !== false) {
        $cnt = 0;
        //$str = "Results:<br><ul>\n";
        
        
        echo '<table style="width:100%">';
        echo '<th>City</th>';
        while($row = mysqli_fetch_assoc($results)) {
            //$str .= "<li>".$row['city'].": ".$row['population']." (".$row['distance_in_miles']." miles) <!--".var_export($row, true)."--></li>\n";
            echo '<tr><td>'.$row['city'].'</td></tr>';
            $cnt++;
        }
        echo '</table>';
        //$str .= "</ul>";
        //$str .= "<br>Total Rows: ".$cnt."</br>";
        //$str .= "<br>Query: ".$sql."<br>";
        
        //echo $str;
        
    } else {
        throw new Exception("<b>Query Failed (". mysql_error().").  Query='".$sql."'</b>");
    }
    
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