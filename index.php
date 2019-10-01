<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use Services\OrderService;
use Services\ProductService;

//$service = new ProductService();
$service = new OrderService();
//var_dump($service->get(1));
var_dump($service->get(1));