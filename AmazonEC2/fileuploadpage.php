<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	function uploadFile(){

		if($("input[name='uploadedFile']").val()!=undefined){
			console.log("We found th file!");
			let file = $("input[name='uploadedFile']").val();
		}else{
			alert("Please select a file.")
			return false;
		}
		let debug = $("input[name='debug']").val();
		let updates = $("input[name='updateCnt']").val();
		
		$.post("http://34.212.128.254/AmazonEC2/fileUploadScript.php",
				{ 
					'uploadedFile': file,
					'debug': debug,
					'updateCnt': updates
				}
				).fail(function(xhr, status, error) {
					console.log("Something whent wrong!"");
					console.log(xhr.responseText);
				}).success(function(){
					console.log("Success!");
				})
		return false;
	};
</script>

<html>			
	<body>
		<form onSubmit="return uploadFile()" method="post" enctype="multipart/form-data">
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