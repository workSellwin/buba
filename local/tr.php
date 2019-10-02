<?php
use \Asdrubael\Utils;

require 'vendor/autoload.php';
include "buildTree.php";

$points = [
    "sections" => 'http://kocmo1c.sellwin.by/Kosmo_Sergey/hs/Kocmo/GetFolder/GoodsOnlyGroup',
];

if( $_GET['mode'] == "get_sections" || !isset($_GET['mode']) ) {
    $uri = 'http://kocmo1c.sellwin.by/Kosmo_Sergey/hs/Kocmo/GetFolder/GoodsOnlyGroup';
}

if( empty($uri) ){
    echo "URL not defined";
    die();
}
$client = new \GuzzleHttp\Client();
$response = $client->request('GET', $uri);

if($response->getStatusCode() == 200){
    $outArr = json_decode($response->getBody(), true);
}
else{
    echo "error: status: " . $response->getStatusCode();
    die();
}

if( $_GET['mode'] == "get_sections" || !isset($_GET['mode']) ) {
    $buildTree = new Utils\BuildTree($outArr);
    $result = $buildTree->createTree();
    echo '<pre>' . print_r($buildTree->getTree(), true) . '</pre>';
}



