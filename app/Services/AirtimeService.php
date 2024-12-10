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
        $user_id = 1; // for example
        $response = null;
        DB::beginTransaction();
        
        try {
            
            $userWallet = $this->walletService->getUserWallet($user_id);
            if($userWallet->balance > $params['amount']) {
                $userWallet->balance = $userWallet->balance - $params['amount'];
                $userWallet->save();
                
                $response = $this->bapService->vendAirtime($params);

                $this->transactionService->createTransaction([
                    'user_id' => $user_id,
                    'reference' => $response['data']['transactionReference'],
                    'amount'    => $response['data']['amount'],
                    'network'   => $params['service_type'],
                    'transaction_type' => 'wallet_debit'
                ]);
                
                $comissionAmount = $this->commissionService->getCommission($response['data']['amount'])->bonus_amount;
            
                $userWallet->balance = $userWallet->balance + $comissionAmount;
                $userWallet->save();

                $this->transactionService->createTransaction([
                    'user_id' => $user_id,
                    'reference' => 'system-reference'.time(),
                    'amount'    => $comissionAmount,
                    'transaction_type' => 'comission_top_up'
                ]);

                DB::commit();

                return [
                    'wallet_balance' => $userWallet->balance
                ];
                
            }

        } catch (\Exception $e) {   
            DB::rollBack();

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

    }
}