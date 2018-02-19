<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	function registerUser(){
		console.log("Verifying registration...");

		var password = $("input[name='password']").val();
		var email = $("input[name='email']").val();

		var logindata = new FormData();
		logindata.append('email', email);
		logindata.append('password', password);
		logindata.append('pageAction', 'createuser'); //Tell the user actions what to do
			
		$.ajax({
			url: "http://34.212.128.254/AmazonEC2/PersonalLibrary/UserActions.php",
			data: logindata,
			contentType: false,
			processData: false,
			type: 'POST',
			success: function(data){
				if(typeof(data['error']) != "undefined") {
					//We got an error back
					
					if(data['error'].indexOf("Duplicate") != -1) {
						//Duplicate User
						alert("This user already has an account.");
					} else {
						alert("Unknown Error:\n" + data['error']);
					}
					console.log(data);
				} else {
					//No error
					console.log("Successful Registration!");
					console.log(data);
					window.location.href = 'http://34.212.128.254/AmazonEC2/PersonalLibrary/libraryHome.php';
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
	<form onsubmit="return registerUser()" method="post" enctype="multipart/form-data">
		User Email: <input type="text" name="email"><br>
		User Password: <input type="password" name="password"><br>
		<input type="submit" name="submitStatus" value="Make Account">
	</form>
	<br>
</html>