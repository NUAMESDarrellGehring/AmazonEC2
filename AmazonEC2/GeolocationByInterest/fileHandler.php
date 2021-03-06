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
      function initMap(locations) {
        

		if(map==undefined && locations!=undefined){
	        var centerVar = {lat: parseFloat(locations[0].latitude), lng: parseFloat(locations[0].longitude)};
            var map = new google.maps.Map(document.getElementById('map'), {
              zoom: 8,
            });
            console.log("Map created");
		}

		if(map!=undefined){	
    		if(map.marker!=undefined){
    			map.marker.setMap(null)
    			console.log("Markers removed.");
    		}
    		map.setCenter(centerVar);
    		console.log("Center zoom set.");
            for(let i=0; i<locations.length; i++){
    			var markerCoords = {lat: parseFloat(locations[i].latitude), lng: parseFloat(locations[i].longitude)};
            	var marker = new google.maps.Marker({
                	position: markerCoords,
                	map: map,
                	title: locations[i].city
            	});
            }
		}
      };
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
					"http://34.212.128.254/AmazonEC2/GeolocationByInterest/locationsByInterest.php", 
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
			   		"bLengthChange": false,
			   		"bFilter": false,
			   		"bSortable": false,
			        "serverSide": true,
			        "stateSave": true,
			        "ajax": {
				        "dataType": "json",
			            "url": "http://34.212.128.254/AmazonEC2/GeolocationByInterest/locationsByInterest.php", //had to change this address
			            "type": "POST",
			            "data": {
				            'action' : 'getData',
			            	'lng': lng,
			            	'lat': lat, 
							'distance': distToSearch
			            }
			        },
			        
			        "columns": [
			            { "data": "city", title: "City", "orderable": false},
			            { "data": "state", title: "State", "orderable": false},
			            { "data": "distance_in_miles", title: "Distance In Miles", "orderable": false}
			        ]

				} ).on( 'xhr', function(e, settings, json) { //xhr is an event that occurs when an ajax action IS COMPLETED 
				    console.log( 'Ajax event occurred. Returned data: ', json );
				    initMap(json.data);
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
    			<input type ="submit" name="submitStatus" value="Submit Request">
    		</form>
    	</div>
	</body>
</html>