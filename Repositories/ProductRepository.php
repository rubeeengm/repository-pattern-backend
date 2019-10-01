<?php

namespace Repositories;

use PDO;
use Models\Product;
use Database\DbProvider;

class ProductRepository {
    private $_db;

    public function __construct() {
        $this->_db = DbProvider::get();
    }

    public function find(int $id): ?Product {
        $result = null;

        $stm = $this->_db->prepare('select * from products where id = :id');
        $stm->execute(['id' => $id]);

        $data = $stm->fetchObject('\\Models\\Product');

        if($data) {
            $result = $data;
        }

        return $result;
    }

    public function findAll() : array {
        $result = [];

        $stm = $this->_db->prepare('select * from products');
        $stm->execute();

        $result = $stm->fetchAll(PDO::FETCH_CLASS, '\\Models\\Product');

        return $result;
    }

    public function add(Product $model): void {
        $stm = $this->_db->prepare(
            'insert into products(name, price, created_at, updated_at) values (:name, :price, :created, :updated)'
        );

        $now = date('Y-m-d H:i:s');

        $stm->execute([
            'name' => $model->name
            , 'price' => $model->price
            , 'created' => $now
            , 'updated' => $now
        ]);
    }

    public function update(Product $model) : void {
        $stm = $this->_db->prepare('
            update products
            set name = :name
            , price = :price
            , updated_at = :updated
            where id = :id
        ');

        $stm->execute([
            'id' => $model->id
            , 'name' => $model->name
            , 'price' => $model->price
            , 'updated' => date('Y-m-d H:i:s')
        ]);
    }

    public function remove(int $id) : void {
        $stm = $this->_db->prepare(
            'delete from products where id = :id'
        );

        $stm->execute(['id' => $id]);
    }
}