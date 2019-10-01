<?php
use \Asdrubael\Utils;
include "buildTree.php";

$outArr = [
    [
        'ID' => 2,
        'PARENT_ID' => 1,
    ],
    [
        'ID' => 3,
        'PARENT_ID' => 1,
    ],
    [
        'ID' => 1,
        'PARENT_ID' => false,
    ],
    [
        'ID' => 4,
        'PARENT_ID' => 3,
    ],
    [
        'ID' => 5,
        'PARENT_ID' => 2,
    ],
    [
        'ID' => 6,
        'PARENT_ID' => false,
    ],
    [
        'ID' => 7,
        'PARENT_ID' => 4,
    ],
    [
        'ID' => 8,
        'PARENT_ID' => 7,
    ],
    [
        'ID' => 9,
        'PARENT_ID' => [6, 8],
    ],
];

$buildTree = new Utils\BuildTree($outArr);
$result = $buildTree->createTree();

echo '<pre>' . print_r($buildTree->getTree(), true) . '</pre>';

