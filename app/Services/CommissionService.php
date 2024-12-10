<?php

namespace App\Services;

use App\Models\Comission;

class CommissionService {

    protected $commissionModel;
    public function __construct(Comission $comission) { 
        $this->commissionModel = $comission;
    }

    public function getCommission($amount) {
        return $this->commissionModel->getCommission($amount);
    }

}