<?php 

$requestUrl = 'http://maps.googleapis.com/maps/api/directions/xml?origin=Milwaukee,WI&destination=Rolling+Meadows,IL&sensor=false';
$response = file_get_contents($requestUrl);
 //note I'm assuming English language
$numTolls = substr_count($response, 'Toll road');
$hasTolls = ($numTolls > 0);
var_dump($hasTolls);
if ($hasTolls) {
    echo '<p>The route has '.$numTolls.' toll roads</p>';
}

?>