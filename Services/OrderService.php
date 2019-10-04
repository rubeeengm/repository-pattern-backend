<?php

namespace Services;

use PDO;
use Models\User;

use Models\Order;
use PDOException;

use Container\Container;
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
    private $_logger;

    public function __construct() {
        $this->_db = DbProvider::get();

        $this->_productRepository = new ProductRepository;
        $this->_userRepository = new UserRepository;
        $this->_orderRepository = new OrderRepository;
        $this->_orderDetailRepository = new OrderDetailRepository;
        $this->_logger = Container::get('logger');
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
            $this->_logger->error($ex->getMessage());
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
            $this->_logger->info('Comenzó la cración de orden');
            $this->_db->beginTransaction();

            $this->_logger->info('Se preparó el modelo para la nueva orden');
            $this->prepareOrderCreation($model);

            $this->_orderRepository->add($model);
            $this->_logger->info('Se creó la nueva orden');
            $this->_logger->info('Se asoció el ID ' . $model->id . ' a la nueva orden');

            $this->_orderDetailRepository->addByOrderId($model->id, $model->detail);
            $this->_logger->info('Se creó el detalle de la orden');

            $this->_db->commit();
            $this->_logger->info('Finalizó la creación de la orden');
        } catch(PDOException $ex) {
            $this->_db->rollback();
            $this->_logger->error($ex->getMessage());
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
}