<?php
require_once __DIR__ . '/vendor/autoload.php';
use Classes\PochtaParser; 

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

checkAccess();

$parser = new PochtaParser('https://www.pochta.ru/offices/'.$_ENV["POSTAL_CODE"]);
$parser->fetch();
$today = $parser->getWorkingStatus();
$hours = $parser->getWorkingHours();

header('Content-Type: application/json'); 

$response = [
    'today' => $today,
    'hours' => $hours,
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);


