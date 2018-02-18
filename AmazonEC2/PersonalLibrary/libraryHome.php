<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	function loginVerify(){
		console.log("Verifying log-in...");

		let password = $("input[name='password']").val();
		let email = $("input[name='email']").val();

		var logindata = new FormData();
		logindata.append('email', email);
		logindata.append('password', password);
		
		$.ajax({
			url: "http://34.212.128.254/AmazonEC2/PersonalLibrary/loginVerify.php",
			data: logindata,
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
		Password: <input type="password" name="password"><br>
		<input type="submit" name="submitStatus" value="Login">
	</form>
</html>