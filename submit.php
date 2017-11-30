<?php
//writing_comp_bot
//PW: C3JvqDZ9vtBv3
//App: ULpoUkW5GPXrJA
//Secret: SuA3hEw86uRF3BCSZLGfnOdAPTk

//curl -X POST -d 'grant_type=password&username=reddit_bot&password=snoo' --user 'p-jcoLKBynTLew:gko_LXELoV07ZBNUXrvWZfzE3aI' https://www.reddit.com/api/v1/access_token
require "vendor/autoload.php";
class_alias('\RedBeanPHP\R','R');
R::setup( 'mysql:host=localhost;dbname=competitions',
        'root', '' ); //for both mysql or mariaDB

function getToken() {
	$url = "https://www.reddit.com/api/v1/access_token";
	$params = array(
	    'grant_type' => 'password',
	    'username' => 'writing_comp_bot',
	    'password' => 'C3JvqDZ9vtBv3'
	);

	$options[CURLOPT_USERAGENT] = 'writing_bot:v1 (by /u/brightcarvings)';
	$options[CURLOPT_RETURNTRANSFER] = true;
	$options[CURLOPT_CONNECTTIMEOUT] = 5;
	$options[CURLOPT_TIMEOUT] = 10;
	$options[CURLOPT_CUSTOMREQUEST] = 'POST';
	$options[CURLOPT_POSTFIELDS] = $params;
	$options[CURLOPT_HTTPAUTH] = true;
	$options[CURLAUTH_BASIC] = true;
	$options[CURLOPT_USERPWD] = 'ULpoUkW5GPXrJA:SuA3hEw86uRF3BCSZLGfnOdAPTk';

	$ch = curl_init($url);
	curl_setopt_array($ch, $options);
	$response_raw = curl_exec($ch);
	$response = json_decode($response_raw);
	curl_close($ch);
	if($response && isset($response->access_token))
		return $response->access_token;
	return 0;
}
$month = 'December';
$competitions  = R::find("competition","active=:active AND closing_date=:month ORDER by type" ,array(':active'=>1, 'month'=>$month));
$content = "# One off and Recurring Writing Competitions for the month of ".$month."\n";

$content = $content."## Competitions ending in ".$month." \n";
$table = "Competition|Origin|Max words|Entry fee|Type\n";
$table = $table."---------|----------|----------|---------|----------|\n";
foreach($competitions as $comp){
	$table = $table."[".$comp->name."](".$comp->url.")|".$comp->country."|".$comp->max_words."|".$comp->fee."|".$comp->type."\n";
}
$content = $content.$table;
$content = $content."## Monthly Competitions \n";
$competitions  = R::find("competition","active=:active AND closing_date=:month ORDER by type" ,array(':active'=>1, 'month'=>'Monthly'));
$monthly = "Competition|Origin|Max words|Entry fee|Type\n";
$monthly = $monthly."---------|----------|----------|---------|----------|\n";
foreach($competitions as $comp){
	$monthly = $monthly."[".$comp->name."](".$comp->url.")|".$comp->country."|".$comp->max_words."|".$comp->fee."|".$comp->type."\n";
}
$content = $content.$monthly;
//print_r($content);
$url = 'https://oauth.reddit.com/api/submit';

$token = getToken();
//print_r($token);
/*
$data = array('kind'=>'self', 'text'=>'test text', 'title'=>'Writing competitions closing in the month of December.', 'api_type'=>'json', 'sr'=>'test');
$headr[] = 'Content-length: 0';
$headr[] = 'Content-type: application/x-www-form-urlencoded';
$headr[] = 'Authorization: bearer '.$token;
$crl = curl_init($url);
curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
curl_setopt($crl, CURLOPT_USERAGENT, 'writing_bot:v1 (by /u/brightcarvings)');
//curl_setopt($crl, CURLOPT_RETURNTRANSFER, TRUE);
//$options[CURLOPT_POSTFIELDS] = $data;
//$options[CURLOPT_CUSTOMREQUEST] = "POST";
//curl_setopt_array($crl, $options);
//curl_setopt($crl, CURLOPT_POSTFIELDS, $data);
curl_setopt($crl, CURLOPT_POST,true);
curl_setopt($crl, CURLOPT_POSTFIELDS, urlencode('api_type=json&extension=json&sendreplies=true&resubmit=true&kind=self&sr=test&title=test title&text=test text'));
$rest = curl_exec($crl);

curl_close($crl);
*/
$postVals = array('kind'=>'self', 'text'=>$content, 'title'=>'Writing competitions closing in the month of December.', 'api_type'=>'json', 'sr'=>'writing');
$ch = curl_init($url);
$options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10
        );
$headers = array("Authorization: bearer {$token}");
$options[CURLOPT_USERAGENT] = 'writing_bot:v1 (by /u/brightcarvings)';
$options[CURLOPT_POSTFIELDS] = $postVals;
$options[CURLOPT_CUSTOMREQUEST] = "POST";
$options[CURLOPT_HEADER] = false;
$options[CURLINFO_HEADER_OUT] = false;
$options[CURLOPT_HTTPHEADER] = $headers;

curl_setopt_array($ch, $options);
$apiResponse = curl_exec($ch);
$response = json_decode($apiResponse);

print_r($response);


