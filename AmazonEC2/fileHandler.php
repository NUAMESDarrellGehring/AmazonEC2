<html>

	<style>
	   .topRight {
	       position: absolute;
	       top: 18px;
	       right: 18px;
	   }
	   
	   .topLeft {
	       position: absolute;
	       top: 18px;
	       left: 18px;
	   }
	   .bottomRight{
	       position: absolute;
	       bottom: 18px;
	       right: 18px;
	   }
	   
	   .bottomLeft{
	       position: absolute;
	       bottom: 18px;
	       left: 18px;
	   }
	   
	</style>

  <head>
    <style>
       #map {
        height: 460px;
        width: 43%;
       }
    </style>
  </head>
  
  <body>
    <div class="bottomRight" id="map"></div>
    <script>
      function initMap() {
        console.log(<?php echo json_encode($combArray[0]['latitude'], JSON_HEX_TAG); ?>);
        console.log(<?php echo json_encode($combArray[0]['longitude'], JSON_HEX_TAG); ?>);
        var centerVar = {lat: parseFloat(<?php echo json_encode($combArray[0]['latitude'], JSON_HEX_TAG); ?>), lng: parseFloat(<?php echo json_encode($combArray[0]['longitude'], JSON_HEX_TAG); ?>)};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 8,
          center: centerVar
          
        });
		<?php foreach($combArray as $row){?>
		var markerCoords = {lat: parseFloat(<?php echo json_encode($row['latitude'], JSON_HEX_TAG); ?>), lng: parseFloat(<?php echo json_encode($row['longitude'], JSON_HEX_TAG); ?>)};
        var marker = new google.maps.Marker({
            position: markerCoords,
            map: map,
            title: <?php echo json_encode($row['city'], JSON_HEX_TAG); ?>
        });
        <?php }?>
  		
        
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBnYeMEUWJEQH0FQKUZhsL3mesL333Vzbg&callback=initMap">
    </script>
  </body>


	<body>
		<head>
			<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css">
			<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
		
		
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
			
			
			<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
			
			
			
		</head> 

        <!-- Tell the browser that this is javascript -->
        <script>
        	console.log("Start");
            $(document).ready(function() {
            	console.log("Ready Start");
            	$("#cityTable").dataTable( {
            		"order": [],
                });
            	console.log("Ready End");
            });
            console.log("End"); 
        </script>

		<br>
		
		<div class="bottomLeft" id="tableDiv">
		<?php 
            if(count($combArray) > 0) { 
		?>    
		<table id="cityTable" style="width:800px">
			<thead>
    			<tr>
    				<th>City</th>
    				<th>State</th>
    				<th>Distance In Miles</th>
    			</tr>
			</thead>
			<tbody>
	   
	   <?php
		  foreach($combArray as $row) {
		?>
					<tr>
	   					<td><?= $row['city'] ?></td>
    					<td><?= $row['state'] ?></td>
    					<td><?= $row['distance_in_miles'] ?></td>
	   				</tr>
	   
	   <?php
		      
		  } ?>
			</tbody>	
		</table>
		<?php  
		}
		?>
		</div>
				
		<script type="text/javascript">

    		function dynamicDataTable(){
				console.log("Start dynamicDataTable.");
        		
				let locToSend = $("input[name='userLocation']").val();
				let distToSearch = $("input[name='userDistOut']").val();

				console.log("locToSend and distToSearch are set.");

				var retrievedArr;
				
				$.post(
					"http://34.212.128.254/AmazonEC2/locationsByInterest.php", 
					{
						'userLocation': locToSend, 
						'userDistOut': distToSearch
					}
				).done(function(data) {
				    console.log(data);
				    retrievedArr = data;
				    retrievedArr = "yella";
				    console.log("Our post has returned data.");
			  	}).fail(function() {
					console.log("Our post has something wrong with it.");
				})
				
				console.log("Our post request is a success.");

				console.log(retrievedArr);
				
    			return false;
    		}
		
		</script>		
				
				
		<div class="topLeft" id="usrInBox">
    		<form onSubmit="return dynamicDataTable()" method="post" enctype="multipart/form-data">
    			<input type="hidden" name="debug" value="1">
    			<br>
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
    	</div>
	</body>
</html>