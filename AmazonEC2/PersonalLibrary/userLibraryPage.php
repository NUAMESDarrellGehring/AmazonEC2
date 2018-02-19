<?php session_start();?>
<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	var sessionActive = "<?= (isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : "") ?>";
	if(sessionActive == "") {
		window.location.href = 'http://34.212.128.254/AmazonEC2/PersonalLibrary/libraryHome.php';
	}else{
		console.log("Success - user id #"+sessionActive);
	}
</script>

<script>
	function addBook(){
		var authorfirst = $("input[name='authorfirst']").val();
		var authorlast = $("input[name='authorlast']").val();
		var title = $("input[name='title']").val();
		
		var bookdata = new FormData();
		bookdata.append('authorfirst', authorfirst);
		bookdata.append('authorlast', authorlast);
		bookdata.append('title', title); 
		bookdata.append('pageAction', "createBook")//Tell the user actions what to do
			
		$.ajax({
			url: "http://34.212.128.254/AmazonEC2/PersonalLibrary/UserActions.php",
			data: bookdata,
			contentType: false,
			processData: false,
			type: 'POST',
			success: function(data){
				if(typeof(data['error']) != "undefined") {
					//We got an error back
					
					if(data['error'].indexOf("Duplicate") != -1) {
						//Duplicate User
						alert("You already entered this book!");
					} else {
						alert("Unknown Error:\n" + data['error']);
					}
					console.log(data);
				} else {
					//No error
					console.log("Successful Addition!");
					console.log(data);
					window.location.href = 'http://34.212.128.254/AmazonEC2/PersonalLibrary/libraryHome.php';
				}
			},
			fail: function(data){
				console.log("Failure!");
				console.log(data);
			}
		});
	}
</script>

<html>
	<form onsubmit="return addBook();" method="post" enctype="multipart/form-data">
		Author's First Name: <input type="text" name="authorfirst"><br>
		Author's Last Name: <input type="text" name="authorlast"><br>
		Book Title: <input type="text" name="title"><br>
		<input type="submit" name="submit" value="Add Book">
	</form>
</html>
