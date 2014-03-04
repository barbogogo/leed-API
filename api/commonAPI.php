<?php

$rep = "../../";

session_start();

require_once($rep.'RainTPL.php');
require_once($rep.'i18n.php');
class_exists('Plugin') or require_once($rep.'Plugin.class.php');
class_exists('MysqlEntity') or require_once($rep.'MysqlEntity.class.php');
class_exists('Update') or require_once($rep.'Update.class.php');
$resultUpdate = Update::ExecutePatch();
class_exists('Feed') or require_once($rep.'Feed.class.php');
class_exists('Event') or require_once($rep.'Event.class.php');
class_exists('Functions') or require_once($rep.'Functions.class.php');
class_exists('User') or require_once($rep.'User.class.php');
class_exists('Folder') or require_once($rep.'Folder.class.php');
class_exists('Configuration') or require_once($rep.'Configuration.class.php');

$feedManager = new Feed();
$eventManager = new Event();
$folderManager = new Folder();
$configurationManager = new Configuration();
$conf = $configurationManager->getAll();

$userManager = new User();
$myUser = (isset($_SESSION['currentUser'])?unserialize($_SESSION['currentUser']):false);

?>
