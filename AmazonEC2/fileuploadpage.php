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

			$.ajax({
    			url:"http://34.212.128.254/AmazonEC2/fileUploadScript.php",
   				data:	{
					'uploadedFile': file,
					'debug': debug,
					'updateCnt': updates
						},
    			type:'POST',
    			contentType: false,
    			processData: false,
			}).fail(function(xhr, status, error) {
				console.log("Something whent wrong!");
				console.log(xhr.responseText);
			}).success(function(){
				console.log("Success!");
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