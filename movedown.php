<?php
require_once('DirectoryMapper.class.php');
require_once('Dir.class.php');

$object = new directoryMapper();
$object->moveDown($object->getById($_GET['id']));

header("Location: index.php");
die();