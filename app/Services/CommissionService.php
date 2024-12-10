<?php

namespace App\Services;

use App\Models\ComissionLookup;

class CommissionService {

    protected $commissionLookupModel;
    public function __construct(ComissionLookup $comissionLookup) { 
        $this->commissionLookupModel = $comissionLookup;
    }

    public function getCommissionLookup($amount) {
        return $this->commissionLookupModel->getCommission($amount);
    }

}