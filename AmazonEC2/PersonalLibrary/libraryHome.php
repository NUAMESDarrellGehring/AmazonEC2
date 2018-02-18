<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	function loginVerify(){
		console.log("Verifying log-in...");

		var password = $("input[name='password']").val();
		var email = $("input[name='email']").val();

		var logindata = new FormData();
		logindata.append('email', email);
		logindata.append('password', password);

		//Ajax post request
		$.ajax({
			url: "http://34.212.128.254/AmazonEC2/PersonalLibrary/loginVerify.php",
			data: logindata,
			contentType: false,
			processData: false,
			type: 'POST',
			success: function(data){
				if(data=='["failure"]'){
					alert("The email or password is invalid. Please try again.");
					console.log(data);
				}else{
					console.log("Successful Login!");
				}
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
	<br>
	<a href="http://34.212.128.254/AmazonEC2/PersonalLibrary/registrationPage.php">
	Click here to register!
	</a>
</html>