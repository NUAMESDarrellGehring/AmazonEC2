<?php session_start();?>
<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	var sessionActive = "<?= (isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : "") ?>";
	if(sessionActive == "") {
		window.location.href = 'http://34.212.128.254/AmazonEC2/PersonalLibrary/libraryHome.php';
	}
</script>

<script>
	
</script>

<html>
Welcome to this page you shou shouldn't be able to see!
</html>
