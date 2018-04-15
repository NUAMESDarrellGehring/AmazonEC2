<?php session_start();?>
<html>
	<head>

    <style>
        .button {
            cursor: pointer;
            height: 100%; /* 100% Full-height */
            width: 30px; /* 0 width - change this with JavaScript */
            position: fixed; /* Stay in place */
            z-index: 1; /* Stay on top */
            top: 0; /* Stay at the top */
            left: 0;
            background-color: #7d0200; /* Cherry*/
            overflow-x: hidden; /* Disable horizontal scroll */
            padding-top: 60px; /* Place content 60px from the top */
            transition: 0.5s; /* 0.5 second transition effect to slide in the sidenav */
        }
    
       .topRight {
           position: absolute;
           top: 18px;
           right: 18px;
           transition: 0.5s;
       }
       
       .topLeft {
           position: absolute;
           top: 18px;
           left: 48px;
           transition: 0.5s;
       }

       .bottomRight{
           position: absolute;
           bottom: 18px;
           right: 18px;
           transition: 0.5s;
       }
       
       .bottomLeft{
           position: absolute;
           bottom: 18px;
           left: 48px;
           transition: 0.5s;
       }
       
        .sidenav {
            height: 100%; /* 100% Full-height */
            width: 0; /* 0 width - change this with JavaScript */
            position: fixed; /* Stay in place */
            z-index: 1; /* Stay on top */
            top: 0; /* Stay at the top */
            left: 0;
            background-color: #111; /* Black*/
            overflow-x: hidden; /* Disable horizontal scroll */
            padding-top: 60px; /* Place content 60px from the top */
            transition: 0.5s; /* 0.5 second transition effect to slide in the sidenav */
        }
        
        /* The navigation menu links */
        .sidenav a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }
        
        /* When you mouse over the navigation links, change their color */
        .sidenav a:hover {
            color: #f1f1f1;
        }
                
        #mainPage {
            position: static;
            transition: 0.5s;
        }
        
        #bookTable {
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .dataTables_scrollBody {min-height:55px}
    </style>
    
 	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.jqueryui.min.css"/>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css">
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
   
    <script type="text/javascript">
        var sessionActive = "<?= (isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : "") ?>";
        if(sessionActive == "") {
        	window.location.href = 'http://34.212.128.254/AmazonEC2/PersonalLibrary/libraryHome.php';
        }else{
        	console.log("Success - user id #"+sessionActive);
        }

    	var menuOpen=false;

        function removeBook(){
    		var authorlast = $("input[name='authorlast']").val();
    		var title = $("input[name='title']").val();

    		var bookdata = new FormData();
    		bookdata.append('authorlast', authorlast);
    		bookdata.append('title', title); 
    		bookdata.append('pageAction', "deleteBook")

    		$.ajax({
    			url: "http://34.212.128.254/AmazonEC2/PersonalLibrary/LibraryActions.php",
    			data: bookdata,
    			contentType: false,
    			processData: false,
    			type: 'POST',
    			success: function(data){
    				if(data['data']['error']=="nonexistant") {
    					//We got an error back
    					alert("That book doesn't exist yet!");
    					console.log(data);
    				} else {
    					//No error
    					console.log("Successful Removal!");
    					console.log(data);
    				}
					dynamicDataTable();
    			},
    			fail: function(data){
    				console.log("Failure!");
    				console.log(data);
    			}
    		});
    		return false;
    	}
        
    	function addBook(){
        	var isbn = $("input[name='isbn']").val();
    		var authorfirst = $("input[name='authorfirst']").val();
    		var authorlast = $("input[name='authorlast']").val();
    		var title = $("input[name='title']").val();

			console.log(authorfirst+" "+authorlast+" "+title);
    		
    		var bookdata = new FormData();
    		bookdata.append('isbn', isbn);
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
    					} else if(data['error'].indexOf("invalidisbn") != -1) {
        					alert("You've entered an invalid ISBN! It must be 13 digits and contain no characters.");
    					} else {
    						alert("Unknown Error:\n" + data['error']);
    					}
    					console.log(data);
    				} else {
    					//No error
    					console.log("Successful Addition!");
    					console.log(data);
    				}
					dynamicDataTable();
    			},
    			fail: function(data){
    				console.log("Failure!");
    				console.log(data);
    			}
    		});
    		return false;
    	}

		function dynamicDataTable(){
            console.log("Start dynamicDataTable.");

			console.log("made it here");

		    //$('#bookTable').on('click', 'tbody td:not(:first-child)', function (e) {
		      //  editor.inline(this);
		    //} ); remove thissss
            
            if ($.fn.DataTable.isDataTable( '#bookTable' ) ) {
            	console.log("Starting Clear...");
              	$('#bookTable').DataTable().clear();
              	console.log("Clear Done.  Destroying....");
              	$('#bookTable').DataTable().destroy();
              	console.log("Done Destroying.");
            }
            
            console.log("About to draw");
            
            $('#bookTable').DataTable( {
                
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
                        'pageAction' : 'getbooks'
                    }
                },
                
                "columns": [
                    { "data": "title", title: "Book", "orderable": false},
                    { "data": "authorfirst", title: "Author First", "orderable": false},
                    { "data": "authorlast", title: "Author Last", "orderable": false},
                    { "data": "isbn", title: "ISBN#", "orderable": false}
                ],
    
                "error": function(xhr, status, error) {
            	  			var err = eval("(" + xhr.responseText + ")");
            	  			alert(err.Message);
            			}
            
            } ).on( 'xhr', function(e, settings, json) { //xhr is an event that occurs when an ajax action IS COMPLETED 
                console.log( 'Ajax event occurred. Returned data: ', json );
            	} 
        	);
            
            console.log("end dynamicDataTable.");
		};

    	function userChoice(){
    		var chosenVal = $('#choice').find(":selected").val();;
    		
			console.log("Beginning userChoice");	
			if(chosenVal==2){
				removeBook();
				console.log("Removing Status: "+chosenVal);
				return false;
			} else if (chosenVal==1){
				addBook();
				console.log("Creating Status: "+chosenVal)
				return false;
			} else {
				console.log("chosenVal "+chosenVal+" is not available.");
				return false;
			}
    	}
    	
    	/* Set the width of the side navigation to 250px */
    	function openNav() {
        	$(".button").css("left", "250");
        	$(".topLeft").css("left", "298");
    	    $(".bottomLeft").css("left", "298");
    		$("#Menu").css("width", "250");
    		menuOpen = true;
    	}

    	/* Set the width of the side navigation to 0 */
    	function closeNav() {
        	$(".button").css("left", "0");
        	$(".topLeft").css("left", "48");
    	    $(".bottomLeft").css("left", "48")
    	    $("#Menu").css("width", "0");
    	    menuOpen = false;
    	}

    	function toggleNav() {
			if(menuOpen==false){
				openNav();
			}else{
				closeNav();
			}
    	}

    	//Do stuff to create an entry
    	function addEntry()
    	{
        	alert("Adding Entry");
    	}

    	let _dialog = null;
    	$(document).ready(function() {
            dynamicDataTable();
            $("#bookTable tbody").on('click', 'tr', function() {
            	let data = $("#bookTable").DataTable().row(this).data();
    			_dialog = $("#creatorForm").dialog({
        	      height: 400,
        	      width: 450,
        	      modal: true,
        	      buttons: {
        	        "Submit": addEntry,
        	        Cancel: function() {
        	          _dialog.dialog( "close" );
        	        }
        	      },
        	      close: function() {
        	        $("#newEntryForm").trigger("reset");
        	      }
        	    });
            });                
                
	   	});

    	
    		
    </script>
    
    </head>
    
    <body>
    	<div id="creatorForm" class="topRight" style:"display: none;">
    		<form id="newEntryForm">
        		Author's First Name: <input type="text" name="authorfirst"><br>
        		Author's Last Name: <input type="text" name="authorlast"><br>
        		Book Title: <input type="text" name="title"><br>
        		ISBN: <input type="text" name="isbn"><br>
    		</form>
    	</div>
    	<div class="sidenav" id="Menu">
    		<a href="#"><b>Your Library</b></a>
    		<a href="#">Forum</a>
    		<a href="#">Help & Support</a>
    	</div>
    	
 		<div class="button" id="menuToggle" onclick="toggleNav()"></div>
 		
	   	<div id="mainPage">
    	   	<div class="bottomLeft" id="tableDiv">
    			<table style="height:500px" id="bookTable" class="display" style="width:800px">
            		<thead>
                        <tr>
                            <th>Book</th>
                            <th>Author First</th>
                            <th>Author Last</th>
                            <th>ISBN#</th>
                        </tr>
                    </thead>
        		</table>
           	</div>
        
    		<div class="topLeft" id="UserSelection">
            	<form onsubmit="return userChoice();" method="post" enctype="multipart/form-data">
            		Author's First Name: <input type="text" name="authorfirst"><br>
            		Author's Last Name: <input type="text" name="authorlast"><br>
            		Book Title: <input type="text" name="title"><br>
            		ISBN: <input type="text" name="isbn"><br>
            		Add/Remove: <select name="choice" id="choice">
        				<option value="1">Add</option>
        				<option value="2">Remove</option>
        			</select><br>
            		<input type="submit" name="submit" value="Submit">
            	</form>
        	</div>
        </div>
        
    </body>
        
</html>
