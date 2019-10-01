<?php

namespace Repositories;

use PDO;
use Models\User;
use Database\DbProvider;

class UserRepository {
    private $_db;

    public function __construct() {
        $this->_db = DbProvider::get();
    }

    public function find(int $id): ?User {
        $result = null;

        $stm = $this->_db->prepare('select * from users where id = :id');
        $stm->execute(['id' => $id]);

        $data = $stm->fetchObject('\\Models\\User');

        if($data) {
            $result = $data;
        }

        return $result;
    }
}