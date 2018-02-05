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

      	
		<div class="bottomLeft" id="tableDiv">
    		<table id="cityTable" style="width:800px">
    		</table>
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
					//data = JSON.parse(data);
					//if(data == false) throw "Invalid JSON";
				    console.log(data);
				    console.log("Our post has returned data (" + data['data_returned'].length + " rows).");

	            	$("#cityTable").dataTable({
	            		order: [],
	            		data : data['data_returned'],
	            		columns: [
	                        { title: "City" },
	                        { title: "State" },
	                        { title: "Distance in Miles" }
	                    ]
	                });
		            
			  	}).fail(function() {
					console.log("Our post has something wrong with it.");
				})
				
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