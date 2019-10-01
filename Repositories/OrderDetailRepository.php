<?php

namespace Repositories;

use PDO;
use Models\OrderDetail;
use Database\DbProvider;

class OrderDetailRepository {
    private $_db;

    public function __construct() {
        $this->_db = DbProvider::get();
    }

    public function findAllByOrderId(int $order_id): Array {
        $result = [];

        $stm = $this->_db->prepare('select * from order_detail where order_id = :order_id');
        $stm->execute(['order_id' => $order_id]);

        return $stm->fetchAll(PDO::FETCH_CLASS, '\\Models\OrderDetail');
    }

    public function addByOrderId(int $orderId, Array $model) : void {
        foreach ($model as $item) {
            $stm = $this->_db->prepare('
                insert into order_detail(order_id, product_id, quantity, price, total, created_at, updated_at)
                values(:order_id, :product_id, :quantity, :price, :total, :created_at, :updated_at)
            ');

            $stm->execute([
                'order_id' => $orderId
                , 'product_id' => $item->product_id
                , 'quantity' => $item->quantity
                , 'price' => $item->price
                , 'total' => $item->total
                , 'created_at' => $item->created_at
                , 'updated_at' => $item->updated_at
            ]);
        }
    }
}