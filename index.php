<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use Services\ProductService;

$service = new ProductService();
//var_dump($service->get(1));
var_dump($service->getAll());