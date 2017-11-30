<?php
require "vendor/autoload.php";
class_alias('\RedBeanPHP\R','R');
R::setup( 'mysql:host=localhost;dbname=competitions',
        'root', '' ); //for both mysql or mariaDB
//R::fancyDebug( TRUE );
switch(@$_REQUEST['type']) {
	case 'short_story':
		$type = 'short story';
	break;
	case 'novel':
		$type = 'novel';
	break;
	case 'flash':
		$type = 'flash fiction';
	break;
	case 'poetry':
		$type = 'poetry';
	break;
	default:
		$type = '';
	break;
}
$args = array();
$sql = " active=:active ";
$args[':active'] = 1;
$month = (isset($_REQUEST["month"])?$_REQUEST["month"]:"");
if($month) {
	$sql = $sql." AND (closing_date=:month OR closing_date='Monthly') ";
	$args[':month'] = $month;
}
$country = (isset($_REQUEST["country"])?$_REQUEST["country"]:"");
if($country) {
	$sql = $sql." AND country LIKE :country ";
	$args[':country'] = "%".$country."%";
}
$words = (isset($_REQUEST["words"])?$_REQUEST["words"]:"");
if($month) {
	$sql = $sql." AND max_words<=:words  ";
	$args[':words'] = $words;
}
if($type) {
	$sql = $sql." AND type=:type ";
	$args[':type'] = $type;
}
$competitions  = R::find("competition",$sql ,$args);
$content = json_encode($competitions, JSON_UNESCAPED_SLASHES);
header('Content-Type: application/json');
echo $content;
?>