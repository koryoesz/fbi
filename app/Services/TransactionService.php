<?php

namespace App\Services;

use App\Repositories\ITransactionRepository;
use App\Models\Transaction;

class TransactionService implements ITransactionRepository{

    protected $transactionModel;
    public function __construct(Transaction $transaction) {
        $this->transactionModel = $transaction;
     }

    public function createTransaction($data)
    {  
        return $this->transactionModel->create($data);
    }
}