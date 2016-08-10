<?php
require_once('DirectoryMapper.class.php');
require_once('Dir.class.php');

$mapper = new directoryMapper();
$dir = $mapper->getById($_GET['id']);
$delete_result = $mapper->deleteNode($dir);

header("Location: index.php?delete_result=".(int) $delete_result);
die();