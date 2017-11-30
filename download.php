<?php
set_time_limit(0);
switch(@$_REQUEST['which']) {
	case 'short_story':
		$request_url = "http://www.christopherfielden.com/short-story-tips-and-writing-advice/short-story-competitions.php";
		$type = 'short story';
	break;
	case 'novel':
		$request_url = "http://www.christopherfielden.com/short-story-tips-and-writing-advice/book-and-novel-competitions.php";
		$type = 'novel';
	break;
	case 'flash':
		$request_url = "http://www.christopherfielden.com/short-story-tips-and-writing-advice/flash-fiction-competitions.php";
		$type = 'flash fiction';
	break;
	case 'poetry':
		$request_url = "http://www.christopherfielden.com/short-story-tips-and-writing-advice/poetry-contests.php";
		$type = 'poetry';
	break;
	default:
		$request_url = "http://www.christopherfielden.com/short-story-tips-and-writing-advice/short-story-competitions.php";
		$type = 'short story';
	break;
}

require "vendor/autoload.php";
use PHPHtmlParser\Dom;
class_alias('\RedBeanPHP\R','R');
R::setup( 'mysql:host=localhost;dbname=competitions',
        'root', '' ); //for both mysql or mariaDB

$dom = new Dom;
$dom->loadFromUrl($request_url);
$html = $dom->outerHtml;
$total = 0;
$tables = $dom->find('table');
$comps = array();
$active = false;
$activity = 0;
foreach($tables as $table) {
	$x=0;
	if($activity != 0) {
		$active = true;
	}
	$activity++;
	$rows = $table->find('tr');
	foreach($rows as $row) {
		if($x===0) { //heading row

		} else {
			if($x % 2 === 1) { //odd row - comp attributes
				$cells = $row->find('td');
				$name = str_replace(array("'", "\"", "&quot;"), "'", $cells[0]->innerHtml);
				$comps[$total]['name'] = strip_tags($name);
				$comps[$total]['url'] = '';
				$link = $cells[0]->find('a');
				if(count($link) > 0) {
					$comps[$total]['url'] = strip_tags($link->getAttribute('href'));
				}
				$comps[$total]['country'] = strip_tags($cells[1]->innerHtml);
				$comps[$total]['closing_date'] = strip_tags($cells[2]->innerHtml);
				$comps[$total]['winner_announcement'] = strip_tags($cells[3]->innerHtml);
				$comps[$total]['max_words'] = strip_tags($cells[4]->innerHtml);
				$comps[$total]['fee'] = strip_tags($cells[5]->innerHtml);
				$comps[$total]['prize'] = strip_tags($cells[6]->innerHtml);
				$comps[$total]['type'] = $type;
				$comps[$total]['active'] = $active;
			}
			if($x % 2 === 0) { //even row - comp notes
				$notes = $row->find('td');
				$text = strip_tags($notes->innerHtml);
				$comps[$total]['notes'] = str_replace(array("'", "\"", "&quot;"), "'", $text);
				$total++;
			}
		}
		$x++;
	}
}
//echo "<pre>";
//print_r($comps);
//echo "</pre>";
foreach($comps as $comp) {
	$competition = R::dispense('competition');
	$competition->name = $comp['name'];
	$competition->url = $comp['url'];
	$competition->country = $comp['country'];
	$competition->closing_date = $comp['closing_date'];
	$competition->winner_announcement = $comp['winner_announcement'];
	$competition->max_words = $comp['max_words'];
	$competition->fee = $comp['fee'];
	$competition->prize = $comp['prize'];
	$competition->type = $comp['type'];
	$competition->active = $comp['active'];
	$competition->notes = $comp['notes'];
	//$id = R::store($competition);

}
$content = json_encode($comps, JSON_UNESCAPED_SLASHES);
header('Content-Type: application/json');
echo $content;
?>
