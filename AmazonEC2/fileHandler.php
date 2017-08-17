<?php
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["uploadedFile"]["name"]);
$uploadOk = 1;
$textFileType = pathinfo($target_file,PATHINFO_EXTENSION);

echo "Works";
if(isset($_POST["submit"])) {
    $check = new SplFileInfo($_FILES["uploadedFile"]["tmp_name"]);
    
    var_dump($check->getExtension());
    
}
else{
    echo "Not true";
}
?>