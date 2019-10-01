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

    public function add(Order &$model) : void {
        $stm = $this->_db->prepare('
            insert into orders(user_id, total, creater_id, created_at, updated_at)
            values(:user_id, :total, :creater_id, :created, :updated)
        ');

        $stm->execute([
            'user_id' => $model->user_id
            , 'total' => $model->total
            , 'creater_id' => $model->creater_id
            , 'created' => $model->created_at
            , 'updated' => $model->updated_at
        ]);

        $model->id = $this->_db->lastInsertId();
    }
}