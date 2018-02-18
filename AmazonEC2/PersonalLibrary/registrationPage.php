<script>
	function registerUser(){
		console.log("Verifying registration...");

		var password = $("input[name='password']").val();
		var email = $("input[name='email']").val();

		var logindata = new FormData();
		logindata.append('email', password);
		logindata.append('password', email);
			
		$.ajax({
			url: "http://34.212.128.254/AmazonEC2/PersonalLibrary/createAccount.php",
			data: logindata,
			contentType: false,
			processData: false,
			type: 'POST',
			success: function(data){
				console.log(data);
			},
			fail: function(data){
				console.log("Failure!");
				console.log(data);
			}
			
		});
		return false;
	}
</script>

<html>
	<form onsubmit="return registerUser()" method="post" enctype="multipart/form-data">
		User Email: <input type="text" name="email"><br>
		User Password: <input type="password" name="password"><br>
		<input type="submit" name="submitStatus" value="Make Account">
	</form>
	<br>
</html>