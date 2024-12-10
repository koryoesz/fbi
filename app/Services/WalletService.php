<?php

namespace App\Services;

use App\Models\Wallet;

class WalletService {

    protected $walletModel;
    public function __construct(Wallet $model) { 
        $this->walletModel = $model;
    }

    public function getUserWallet($user_id) {
        return $this->walletModel->where("user_id", $user_id)->first();
    }
}