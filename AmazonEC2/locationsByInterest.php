<?php

$action = "";
if(isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
}

//Figure out what we're trying to do

try {
    switch(strtolower($action)) {
        
        //Get the data
        case "getdata":
            $lat = null;
            $lng = null;
            $distance = null;
            $length = null;
            $start = null;
            
            if(isset($_REQUEST['lat']) && $_REQUEST['lat'] != "") {
                $lat = $_REQUEST['lat'];
            } else{
                throw new Exception("Missing required parameter, lat");
            }
            
            if(isset($_REQUEST['lng']) && $_REQUEST['lng'] != "") {
                $lng = $_REQUEST['lng'];
            } else{
                throw new Exception("Missing required parameter, lng");
            }
            
            if(isset($_REQUEST['distance']) && $_REQUEST['distance'] != "") {
                $distance = $_REQUEST['distance'];
            } else{
                throw new Exception("Missing required parameter, distance");
            }
            
            if(isset($_REQUEST['length']) && $_REQUEST['length'] != "") {
                $length = $_REQUEST['length'];
            }
            
            if(isset($_REQUEST['start']) && $_REQUEST['start'] != "") {
                $start = $_REQUEST['start'];
            }
                
            $rowCount = 0;
            $data = getData($lat, $lng, $distance, $rowCount, $length, $start);
            
            header('Content-Type: application/json');
            echo json_encode(array(
                "data" => $data,
                "recordsTotal" => $rowCount,
                "recordsFiltered" => $rowCount,
            ));
            exit;
            
        break;
        
        //GEO Code an address
        case "geocodeaddress":
            $userLocation = $_REQUEST['userLocation'];
            
            $data = array();
            
            if(isset($_REQUEST['userLocation'])){
                $userCoords =  geoCodeAddress($userLocation);
                debugLog("Longitude of user is: ".$userCoords[0]);
                debugLog("<br>");
                debugLog("Latitude of user is: ". $userCoords[1]);
                debugLog("<br>");
                $data['lng'] = $userCoords[0];
                $data['lat'] = $userCoords[1];
            } else {
                $data['error'] = "Unable to GEO Locate Address (".$userLocation.")";
            }
            
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
            
        break;
        
        default:
            throw new Exception("Unsupported Action: ".$action);
    }
} catch(Exception $ex) {
    header('Content-Type: application/json');
    echo json_encode(array( "error" => $ex->getMessage()) );
    exit;
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

function getData($lat, $lng, $distance, &$rowCount = null, $length = null, $start = null)
{
    if($lat == null) throw new Exception("lat cannot be null");
    if($lng == null) throw new Exception("lng cannot be null");
    if($distance == null) throw new Exception("distance cannot be null");
    
    $servername = "localhost";
    $username = "root";
    $password = "skull71";
    
    $connSearch = new mysqli($servername, $username, $password);
    
    if ($connSearch->connect_error){
        throw new Exception("Connection failed: " . $connSearch->connect_error);
    }
    
    $sql = "USE cityInfoDB;";
    
    $connSearch->query($sql);
    
    debugLog("<br>Query1: ".$sql."\n<br>");
    
    $sql = "
        SELECT SQL_CALC_FOUND_ROWS 
        	city, state,
        	(3963.17 * ACOS(COS(RADIANS(latpoint))
                 * COS(RADIANS(latitude))
                 * COS(RADIANS(longpoint) - RADIANS(longitude))
                 + SIN(RADIANS(latpoint))
                 * SIN(RADIANS(latitude)))) AS distance_in_miles
         FROM cityInfo
         JOIN (
             SELECT ".$lat." AS latpoint, ".$lng." AS longpoint
        ) AS p ON 1=1
        HAVING
            distance_in_miles <= ".($distance+20)."
        ORDER BY
        	-((population/1000)-(distance_in_miles^2))";
    
    if($length != null && $start != null) {
        $sql .= " LIMIT ".$start.", ".$length;
    }
    
    $results = $connSearch->query($sql);
    $results2 = $connSearch->query("select FOUND_ROWS() as total");
    
    $data = array();
    if($results !== false) {
        $cnt = 0;
        while($row = mysqli_fetch_assoc($results)) {
            $data[] = $row;
        }
        
        $rowCount = mysqli_fetch_array($results2)['total'];
        
    } else {
        throw new Exception("<b>Query Failed (". mysql_error().").  Query='".$sql."'</b>");
    }
    
    return $data;
    
    //the google api key is AIzaSyBnYeMEUWJEQH0FQKUZhsL3mesL333Vzbg
    //debugLog("Test: We've reached the end of this program!!!"); //Signals end of program
}

?>