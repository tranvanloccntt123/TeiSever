<?php
 
 
//delete this line if you don't like to use a password but keep in mind others can send files to this script as well so I not recommend it
if($_POST['pwd'] != md5('teievantosource')){exit;}
 
 
//if a json POST variable is set
if(isset($_POST['json'])){
    //get rid of some chunk
    $filename = 'documentation.json';
    $json = str_replace('\\\\','\\',str_replace('\\"','"',$_POST['json']));
    //and save it somewhere
    write($filename,$json);
    //output the URL of the JSON (required for the save function)
    echo 'http://docs.revaxarts.com/'.$filename;
    exit;
}
 
//we have a file upload
if(!empty($_FILES)){
    //handle it as a fileupload
    move_uploaded_file($_FILES["file"]["tmp_name"], $_FILES["file"]["name"]);
}
 
 
//file_put_contents doesn't work everywhere
function write($filename, $data = ''){
    if (!$handle = fopen($filename, "w+")) {
        return false;
        exit;
    }
    if (!fwrite($handle, $data)) {
        return false;
        exit;
    }
 
    fclose($handle);
    return true;
}
 
?>