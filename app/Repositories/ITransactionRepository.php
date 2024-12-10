<?php

namespace App\Repositories;

interface ITransactionRepository {
    public function createTransaction($data);
}