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
}