<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
		function uploadFile(){
			if($('#fileInput').prop('files')[0]!=undefined){
				console.log("We found the file!");
				var file = $('#fileInput').prop('files')[0];
			}else{
				alert("Please select a file.")
				return false;
			}
			var debug = $("input[name='debug']").val();
			var updates = $("input[name='updateCnt']").val();

		    var dataToSend = new FormData();                  
		    dataToSend.append('uploadedFile', file);
		    dataToSend.append('debug', debug);
		    dataToSend.append('updateCnt', updates);
			
			$.ajax({
    			url:"http://34.212.128.254/AmazonEC2/GeolocationByInterest/fileUploadScript.php",
   				data: dataToSend,
    			type:'POST',
    			contentType: false,
    			processData: false,
    			success: 
        			function(data){
    					console.log("Success!");
    					console.log(data);
    				},
				fail: 
					function(data) {
    					console.log("Something went wrong!");
    					console.log(data);
					}
			});
			
			return false;
	    }
</script>

<html>			
	<body>
		<form onSubmit="return uploadFile()" method="post" enctype="multipart/form-data">
			------- Debug/File Update Section -------
			<br>
			Input File: 
			<input type="file" name="uploadedFile" id="fileInput">
			<br>
			Max Inserts: <input type="text" value="10" name="updateCnt"> (Does Nothing Anymore, Really)
			<br>
			Debug: (Does Nothing Anymore, Really)
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