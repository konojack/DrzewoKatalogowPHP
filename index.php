<?php 

require_once('Dir.class.php');

function displayTree(dir $dir)
{
    
    echo '<div id="displayTree"><ul style="font-size: 26px; color: #EFECCA; list-style-type: square;">';
    echo '<li>';
    
        if($dir->isRoot()){
            echo $dir->getName();
        }else{
            echo '<a href="?akcja=edytuj&id='.$dir->getId().'" 
            style="text-decoration: none; color: #EFECCA;">'.
            $dir->getName().
            '</a>
            <a href="delete.php?id='.$dir->getId().'" 
            style="text-decoration: none; color: red;">
            <img src="delete-icon.png" style="height: 18px; width: 18px;" />
            </a>';
            
                if($dir->getPriority() != 1){
                    echo '<a href="moveup.php?id='.$dir->getId().'">
                    <img src="up-icon.png" style="height: 18px; width: 18px;" /></a>';
                }
                
                if($dir->getParent()->getChildrenAmount() != $dir->getPriority()){
                    echo '<a href="movedown.php?id='.$dir->getId().'">
                    <img src="down-icon.png" style="height: 18px; width: 18px;" /></a>';
                }
                
        }
    echo '</li>';
    
        foreach($dir->getChildrensInOrder() as $children)
        {
            echo displayTree($children);
        }
        
    echo '</ul></div>';
    
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-type" content="text/html" charset="utf-8">
	<title>Drzewo Katalogów</title>
	<link rel="stylesheet" type="text/css" href="style.css">
    
</head>

<body>

<div>
<div id="header"><h1 id="h1_header">Drzewo Katalogów</h1></div>
</div>

<?php

$object = new directoryMapper();
$object->getConnect();


    if(isset($_GET['result']) && $_GET['result'] == directoryMapper::ADD_TRUE) {
        echo '<h3 style="color:red;">Katalog został dodany.</h1>';
    } 

    if(isset($_GET['result']) && $_GET['result'] == directoryMapper::ADD_FALSE){
        echo '<h3 style="color:red;">Taki katalog już istnieje, podaj inną nazwę dla katalogu!</h1>';
    }
    
    if(isset($_GET['result_name']) && $_GET['result_name'] == 0){
        echo '<h3 style="color:red;">Nie podano nazwy katalogu!</h1>';
    }
    
    if(isset($_GET['result']) && $_GET['result'] == directoryMapper::UPDATE_TRUE) {
        echo '<h3 style="color:red;">Katalog został zedytowany.</h1>';
    } 
    
    if(isset($_GET['result']) && $_GET['result'] == directoryMapper::UPDATE_FALSE){
        echo '<h3 style="color:red;">Katalog nie został zedytowany!</h1>';
    }
    
    if(isset($_GET['delete_result']) && $_GET['delete_result'] == 1){
        echo '<h3 style="color:red;">Katalog został usunięty.</h1>';
    }
    
    if(isset($_GET['white_space']) && $_GET['white_space'] == 1){
        echo '<h3 style="color:red;">Nazwa zawiera same spacje, popraw nazwę!</h1>';
    }



    if(isset($_GET['akcja']) && $_GET['akcja'] == 'edytuj'){
    
        $dir = $object->getById($_GET['id']);
        
?>

<div id="editDir">     
<h2>Edytuj katalog</h2>
    <form action="save.php" method="post">
        <input readonly name="id" size="5" type="hidden" value="<?php echo $dir->getId();?>">
        <input readonly name="priority" size="5" type="hidden" value="<?php echo $dir->getPriority();?>">
        Nazwa: <input name="name" size="40" value="<?php echo $dir->getName();?>">
        Katalog nadrzędny: <select name="parent_id" size="1">

<?php

    $objects = $object->getAllToSelectExcludeChildrens($dir);
	   foreach($objects as $temp_dir){
	           $selected = "";
            if($temp_dir->getId() == $dir->getParentId()){
                $selected = 'selected';
            }
	        echo '<option '.$selected.' value="'.$temp_dir->getId().'">'.$temp_dir->getName()."</option>\n";
	   }
?>
        <input type="submit" action="save.php" name="zmien" value="Zmień">
        <input type="reset" value="Resetuj">
    </form>
</div>
    
<?php
    }
?>

<div id="addDir">
    <h2>Dodaj katalog</h2>
        <form action ="save.php" method="post">
            <input type="hidden" value="0" name="id">
            Nazwa: <input name="name" size="5">
            Katalog docelowy: <select name="parent_id" size="1">


<?php
    $temp_dir = $object->getAllToSelect();
    
        foreach($temp_dir as $dir){
            echo '<option value="'.$dir->getId().'">'.$dir->getName()."</option>\n";
        }
                            
?>
            </select>           
            <input type="submit" action="save.php" name="wprowadz" value="Dodaj">
            <input type="reset" value="Resetuj">
        </form>
</div>
                
<?php

$mapper = new directoryMapper();
$root = $mapper->getRoot();
displayTree($root);

?>

</body>
</html>