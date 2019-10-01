<?php

namespace Repositories;

use PDO;
use Models\Order;
use Database\DbProvider;

class OrderRepository {
    private $_db;

    public function __construct() {
        $this->_db = DbProvider::get();
    }

    public function find(int $id): ?Order {
        $result = null;

        $stm = $this->_db->prepare('select * from orders where id = :id');
        $stm->execute(['id' => $id]);

        $data = $stm->fetchObject('\\Models\\Order');

        if($data) {
            $result = $data;
        }

        return $result;
    }
}