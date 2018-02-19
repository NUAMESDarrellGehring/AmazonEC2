<?php session_start();?>
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
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
			url: "http://34.212.128.254/AmazonEC2/PersonalLibrary/LibraryActions.php",
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

<script>

	$(document).ready(function() {
        console.log("Start dynamicDataTable.");
        
        if ($.fn.DataTable.isDataTable( '#cityTable' ) ) {
        	console.log("Starting Clear...");
          	$('#cityTable').DataTable().clear();
          	console.log("Clear Done.  Destroying....");
          	$('#cityTable').DataTable().destroy();
          	console.log("Done Destroying.");
        }
        
        console.log("About to draw");
        
        $('#cityTable').DataTable( {
        		"bLengthChange": false,
        		"bFilter": false,
        		"bSortable": false,
            "serverSide": true,
            "stateSave": true,
            "ajax": {
                "dataType": "json",
                "url": "http://34.212.128.254/AmazonEC2/PersonalLibrary/LibraryActions.php", //had to change this address
                "type": "POST",
                "data": {
                    'action' : 'getbooks',
                }
            },
            
            "columns": [
                { "output": "title", title: "Book", "orderable": false},
                { "output": "authorfirst", title: "Author First", "orderable": false},
                { "output": "authorlast", title: "Author Last", "orderable": false}
            ]
        
        } ).on( 'xhr', function(e, settings, json) { //xhr is an event that occurs when an ajax action IS COMPLETED 
            console.log( 'Ajax event occurred. Returned data: ', json );
        } );
        
        console.log("end dynamicDataTable.");
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
