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
		
		
			<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
			
			
			<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
			
			
			
		</head> 

      	
		<div class="bottomLeft" id="tableDiv">
    		<table id="cityTable" class="display" style="width:800px"></table>
	   </div>
				
		<script type="text/javascript">

			function geoCodeAddress() {
				let locToSend = $("input[name='userLocation']").val();

				$.post(
					"http://34.212.128.254/AmazonEC2/locationsByInterest.php", 
					{
						'action' : "geoCodeAddress",
						'userLocation': locToSend 
					}
				).done(function(data) {
					if(typeof(data['error']) != "undefined") {
						//error!
						alert(data['error']);
					} else {
						//data!!
						dynamicDataTable(data['lng'], data['lat']);
					}
		  		}).fail(function() {
						console.log("Our post has something wrong with it.");
				});
				
				return false;

			}
		
    		function dynamicDataTable(lng, lat){
				console.log("Start dynamicDataTable.");
        		
				let distToSearch = $("input[name='userDistOut']").val();


				if ($.fn.DataTable.isDataTable( '#cityTable' ) ) {
					console.log("Starting Clear...");
				  	$('#cityTable').DataTable().clear();
				  	console.log("Clear Done.  Destroying....");
				  	$('#cityTable').DataTable().destroy();
				  	console.log("Done Destroying.");
				}
			
				console.log("About to draw");
				
				$('#cityTable').DataTable( {
			        "processing": true,
			        "serverSide": true,
			        "ajax": {
			            "url": "http://34.212.128.254/AmazonEC2/locationsByInterest.php",
			            "type": "POST",
			            "contentType": "application/json; charset=utf-8",
			            "dataType": "json",
			            "data": {
				            'action' : 'getData',
			            	'lng': lng,
			            	'lat': lat, 
							'distance': distToSearch
			            }
			        },
			        "columns": [
			            { "data": "city", title: "City"},
			            { "data": "state", title: "State"},
			            { "data": "distance_in_miles", title: "Distance In Miles" }
			        ]
			    } );

				console.log("end dynamicDataTable.");
				
				return false;
    		}
		
		</script>		
				
				
		<div class="topLeft" id="usrInBox">
    		<form onSubmit="return geoCodeAddress()" method="post" enctype="multipart/form-data">
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