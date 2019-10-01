<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use Models\Order;
use Models\OrderDetail;
use Services\OrderService;
use Services\ProductService;

//$service = new ProductService();
$service = new OrderService();
//var_dump($service->get(1));
//var_dump($service->get(1));

$model = new Order();
$model->user_id = 1;
$model->creater_id = 2;
$detail1 = new OrderDetail;
$detail1->product_id = 1;
$detail1->price = 2500;
$detail1->quantity = 2;
$detail2 = new OrderDetail;
$detail2->product_id = 1;
$detail2->price = 2500;
$detail2->quantity = 2;
$model->detail[] = $detail1;
$model->detail[] = $detail2;
$result = $service->create($model);