<?php

namespace Services;

use PDOException;
use Models\Product;
use Repositories\ProductRepository;

class ProductService {
    private $_productRepository;
    private $_logger;

    public function __construct() {
        $this->_productRepository = new ProductRepository;
        $this->_logger = Container::get('logger');
    }

    public function getAll() : Array {
        $result = [];

        try {
            $result = $this->_productRepository->findAll();
        } catch(PDOException $ex) {
            $this->_logger->error($ex->getMessage());
        }

        return $result;
    }

    public function get(int $id) : ?Product {
        $result = null;

        try {
            $result = $this->_productRepository->find($id);
        } catch(PDOException $ex) {
            $this->_logger->error($ex->getMessage());
        }

        return $result;
    }

    public function create(Product $model): void {
        try {
            $result = $this->_productRepository->create($model);
        } catch(PDOException $ex) {
            $this->_logger->error($ex->getMessage());
        }
    }

    public function update(Product $model) : void {
        try {
            $result = $this->_productRepository->update($model);
        } catch(PdoException $ex) {
            $this->_logger->error($ex->getMessage());
        }
    }

    public function delete(int $id) : void {
        try {
            $result = $this->_productRepository->remove($id);
        } catch(PDOException $ex) {
            $this->_logger->error($ex->getMessage());
        }
    }
}