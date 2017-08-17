<?php
$files = glob("uploads/");
foreach($files as $file){
    if(is_file($file))
       unlink($file);
}

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["uploadedFile"]["name"]);
$uploadOk = 1;
$textFileType = pathinfo($target_file,PATHINFO_EXTENSION);

//Is it a valid file type? -------------------
    if($textFileType != "docx" && $textFileType != "doc" && $textFileType != "txt"){
        echo "File Type Is Invalid - Valid Types Are: .docx, .doc, or .txt";
        echo "<br>";
        $uploadOk = 0;
    }
    
    if($_FILES["uploadedFile"]["size"] > 600000){
        echo "File Is Too Large: Max Size Is 600 KB";
        echo "<br>";
        $uploadOk = 0;
    }
    
    if($uploadOk == 0){
        echo "File Upload Failed";
        echo "<br>";
    }else{
        echo "File Upload Successful";
        echo "<br>";
    }
    
    
?>