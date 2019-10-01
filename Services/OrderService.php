<?php

namespace Services;

use PDO;
use Models\User;

use Models\Order;
use PDOException;
use Models\Product;
use Models\OrderDetail;

use Database\DbProvider;
use Repositories\UserRepository;
use Repositories\OrderRepository;
use Repositories\ProductRepository;
use Repositories\OrderDetailRepository;

class OrderService {
    private $_db;
    
    private $_productRepository;
    private $_userRepository;
    private $_orderRepository;
    private $_orderDetailRepository;

    public function __construct() {
        $this->_db = DbProvider::get();

        $this->_productRepository = new ProductRepository;
        $this->_userRepository = new UserRepository;
        $this->_orderRepository = new OrderRepository;
        $this->_orderDetailRepository = new OrderDetailRepository;
    }

    public function get(int $id): ?Order {
        $result = null;

        try {
            $data = $this->_orderRepository->find($id);

            if($data) {
                $result = $data;

                //Client
                $result->client = $this->_userRepository->find($result->user_id);

                //Creater
                $result->creater = $this->_userRepository->find($result->creater_id);

                //Detail
                $result->detail = $this->getDetail($result->id);
            }
        } catch(PDOException $ex) {
            var_dump($ex);
        }

        return $result;
    }

    private function getDetail(int $order_id) : array {
        $result = $this->_orderDetailRepository->findAllByOrderId($order_id);

        foreach ($result as $item) {
            $item->product = $this->_productRepository->find($item->product_id);
        }

        return $result;
    }

    public function create(Order $model): void {
        try {
            $this->_db->beginTransaction();

            $this->prepareOrderCreation($model);
            $this->orderCreate($model);
            $this->orderDetailCreate($model);

            $this->_db->commit();
        } catch(PDOException $ex) {
            $this->_db->rollback();
        }
    }

    private function prepareOrderCreation(Order &$model) : void {
        $now = date('Y-m-d H:i:s');

        $model->created_at = $now;
        $model->updated_at = $now;

        foreach ($model->detail as $item) {
            $item->total = $item->price * $item->quantity;
            $item->created_at = $now;
            $item->updated_at = $now;

            $model->total += $item->total;
        }
    }

    private function orderCreate(Order &$model) : void {
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

    private function orderDetailCreate(Order $model) : void {
        foreach ($model->detail as $item) {
            $stm = $this->_db->prepare('
                insert into order_detail(order_id, product_id, quantity, price, total, created_at, updated_at)
                values(:order_id, :product_id, :quantity, :price, :total, :created_at, :updated_at)
            ');

            $stm->execute([
                'order_id' => $model->id
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