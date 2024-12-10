<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\BapService;
use App\Repositories\IAirtimeRepository;
use App\Services\TransactionService;
use App\Services\WalletService;
use App\Services\CommissionService;

class AirtimeService implements IAirtimeRepository {

    protected BapService $bapService;
    protected TransactionService $transactionService;
    protected WalletService $walletService;
    protected CommissionService $commissionService;
    public function __construct(BapService $bapServ, TransactionService $transactionServ, WalletService $walletServ, CommissionService $commissionServ) {
        $this->bapService = $bapServ;
        $this->transactionService = $transactionServ;
        $this->walletService = $walletServ;
        $this->commissionService = $commissionServ;
    }
    public function vend($params)
    {
        $user_id = 12; // for example
        $response = null;
        // DB::beginTransaction();
        
        try {
        
            $userWallet = $this->walletService->getUserWallet($user_id);
        
            if($userWallet->balance > $params['amount']) {
                $userWallet->balance = $userWallet->balance - $params['amount'];
                $userWallet->save();
           
                $response = $this->bapService->vendAirtime($params);

                $this->transactionService->createTransaction([
                    'user_id' => $user_id,
                    'reference' => $response['data']['transactionReference'],
                    'amount'    => $params['amount'],
                    'network_provider'   => $params['service_type'],
                    'transaction_type' => 'wallet_debit'
                ]);
                
                $comissionAmount = $this->commissionService->getCommissionLookup($params['amount']);

                if(!empty($comissionAmount)) {
                    $userWallet->balance = $userWallet->balance + $comissionAmount->bonus_amount;
                    $userWallet->save();

                    $this->transactionService->createTransaction([
                        'user_id' => $user_id,
                        'reference' => 'system-reference'.time(),
                        'amount'    => $comissionAmount->bonus_amount,
                        'transaction_type' => 'comission_top_up'
                    ]);
                }

                return [
                    'wallet_balance' => $userWallet->balance
                ];
                
            } else {
                return [
                    'status' => 'success',
                    'message' => 'Insufficient wallet balance'
                ];
            }

        } catch (\Exception $e) {   
            // DB::rollBack();
            dd($e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

    }
}