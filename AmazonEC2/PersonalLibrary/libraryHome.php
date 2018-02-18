<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	function loginVerify(){
		console.log("Verifying log-in...");

		var password = $("input[name='password']").val();
		var email = $("input[name='email']").val();

		var logindata = new FormData();
		logindata.append('email', password);
		logindata.append('password', email);
		
		$.ajax({
			url: "http://34.212.128.254/AmazonEC2/PersonalLibrary/loginVerify.php",
			data: logindata,
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
	<form onsubmit="return loginVerify()" method="post" enctype="multipart/form-data">
		Email: <input type="text" name="email"><br>
		Password: <input type="text" name="password"><br>
		<input type="submit" name="submitStatus" value="Login">
	</form>
</html>