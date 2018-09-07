<?php

include_once getcwd() . '/class/OrderProcessor.php';
include_once getcwd() . '/class/Inventory.php';
include_once getcwd() . '/class/ProductsSold.php';
include_once getcwd() . '/class/ProductsPurchased.php';
include_once getcwd() . '/class/Products.php';

// get all needed classes to be dependency injected
$Inventory         = new Inventory();
$ProductsSold      = new ProductsSold();
$ProductsPurchased = new ProductsPurchased();
$Products          = new Products();

echo "Kindly input the json file path:";
$handle = fopen ("php://stdin","r");
$filePath = trim(fgets($handle)); // we need to trim the inputted json file path to remove extra spaces or lines

// process the json file given
$OrderProcessor = new OrderProcessor($Inventory, $ProductsSold, $ProductsPurchased, $Products);
$OrderProcessor->processFromJson($filePath);
