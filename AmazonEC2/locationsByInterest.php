<?php

$servername = "localhost";
$username = "root";
$password = "skull71";

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


$userLocation = $_REQUEST['userLocation'];
$userSearch = $_REQUEST['userDistOut'];

if(isset($_REQUEST['userLocation'])){
    
    $userCoords =  geoCodeAddress($userLocation);
    debugLog("Longitude of user is: ".$userCoords[0]);
    debugLog("<br>");
    debugLog("Latitude of user is: ". $userCoords[1]);
    debugLog("<br>");
}

$connSearch = new mysqli($servername, $username, $password);

if ($connSearch->connect_error){
    die("Connection failed: " . $connSearch->connect_error);
}

//Array for our results from query (if any)
$resultsArr = array();

if(isset($userSearch)) {
    
    $sql = "USE cityInfoDB;";
    
    $connSearch->query($sql);
    
    debugLog("<br>Query1: ".$sql."\n<br>");
    
    $sql = "
        SELECT
        	city, state,
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
            distance_in_miles <= ".($userSearch+20)."
        ORDER BY
        	-((population/1000)-(distance_in_miles^2))
        LIMIT 15;";
    
    $results = $connSearch->query($sql);
    $data = array();
    if($results !== false) {
        $cnt = 0;
        while($row = mysqli_fetch_assoc($results)) {
            $data[] = $row;
        }
        
    } else {
        throw new Exception("<b>Query Failed (". mysql_error().").  Query='".$sql."'</b>");
    }
    
    $returnData = array(
        "data_returned" => $data,
        "longitude" => $userCoords[0],
        "latitude" => $userCoords[1]
    );
    
    header('Content-Type: application/json');
    
    echo json_encode($returnData);
    
    //the google api key is AIzaSyBnYeMEUWJEQH0FQKUZhsL3mesL333Vzbg
    debugLog("Test: We've reached the end of this program!!!"); //Signals end of program
}

?>