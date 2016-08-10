<?php
require_once('DirectoryMapper.class.php');
require_once('Dir.class.php');

$mapper = new directoryMapper();
$dir = new dir($_POST['id'], trim(htmlspecialchars($_POST['name'])), $_POST['parent_id'], $_POST['priority']);
    
    if(ctype_space($_POST['name'])){
        header("Location: index.php?white_space=1"); die;
    }
    
    if(!empty($_POST['name'])){
        $result = $mapper->save($dir);
    }else{
        header("Location: index.php?result_name=0"); die;
    }

header("Location: index.php?result=".(int) $result);
die();