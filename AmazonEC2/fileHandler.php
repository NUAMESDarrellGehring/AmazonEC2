<?php
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["uploadedFile"]["name"]);
$uploadOk = 1;
$textFileType = pathinfo($target_file,PATHINFO_EXTENSION);

echo $textFileType;

if($textFileType == "docx" || $textFileType == "doc" || $textFileType == "txt"){
    
    echo "Valid File Type";
    
}else{

    echo "File Type Is Invalid";

}
   
?>