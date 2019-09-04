<?php
$defaultAppDir = "/home/pi/APPS";

header('Access-Control-Allow-Origin: *');

function listDirJson($dir){
    header('Content-Type: application/json');

    $list = array(); //main array

    if(is_dir($dir)){
        if($dh = opendir($dir)){
            while(($file = readdir($dh)) != false){

                if($file == "." or $file == ".."){
                    //...
                } else { //create object with two fields
                    $list3 = array(
                    'file' => $file);
                    array_push($list, $list3);
                }
            }
        }

        $return_array = array('files'=> $list);

        echo json_encode($return_array);
    }
}

function runApp($appToStart) {
    global $defaultAppDir;
    $appToStart = stripcslashes($appToStart);
    if(file_exists($defaultAppDir.'/'.$appToStart)){
        shell_exec($defaultAppDir.'/'.$appToStart.'  > /dev/null 2>&1 & disown');
        echo "started app: ".$appToStart;
    }else{
        echo "app ".$defaultAppDir.'/'.$appToStart." does not exist";
    }
}

function uploadPictureFile(){
    global $defaultAppDir;
    $valid_extensions = array('jpeg', 'jpg', 'png'); // valid extensions
//    $path = '/var/www/html/image_uploads/'; // upload directory
    $path = '/tmp/'; // upload directory
    if(!empty($_FILES['image']))
    {
        $img = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        // get uploaded file's extension
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        // can upload same image using rand function
        $final_image = rand(1000,1000000).$img;

        // check's valid format
        if(in_array($ext, $valid_extensions))
        {
            $path = $path.strtolower($final_image);
            if(move_uploaded_file($tmp,$path)){
                //run the app
                $startApp = $defaultAppDir."/Picture -s ".$_POST['prescaler']." ".$path."  > /dev/null 2>&1 & disown";
                echo "$startApp";
                shell_exec($startApp);
            }
        }
    }else{
        echo 'invalid';
    }
}


if($_GET["do"] == "run"){
    $app = $_GET["app"];
    //todo: secure app variable
    runApp($app);
}else if($_GET["do"] == "listAppDir"){
    listDirJson($defaultAppDir);
}else if($_GET["do"] == "uploadPicture"){
    uploadPictureFile();
}

?>