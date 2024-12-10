<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\BapService;
use App\Repositories\IAirtimeRepository;
use App\Services\TransactionService;
use App\Services\WalletService;
use App\Services\CommissionService;
use App\Services\ShagoService;

class AirtimeService implements IAirtimeRepository {

    protected BapService $bapService;
    protected TransactionService $transactionService;
    protected WalletService $walletService;
    protected CommissionService $commissionService;
    protected ShagoService $shagoService;
    public function __construct(BapService $bapServ, TransactionService $transactionServ, WalletService $walletServ, 
            CommissionService $commissionServ, ShagoService $shagoServ) {
        $this->bapService = $bapServ;
        $this->transactionService = $transactionServ;
        $this->walletService = $walletServ;
        $this->shagoService = $shagoServ;
        $this->commissionService = $commissionServ;
    }
    public function vend($params, $type = '')
    {
        $user_id = 15; // for example authenticated user should be retrieved from either here or before
        $response = null;
        
        try {
            return DB::transaction(function () use ($params, $user_id, $type) {
                $userWallet = $this->walletService->getUserWallet($user_id);
            
                if($userWallet->balance > $params['amount']) {
                    $userWallet->balance = $userWallet->balance - $params['amount'];
                    $userWallet->save();

                    if($type == "bap"){
                        $response = $this->bapService->vendAirtime($params);
                    } else if($type == "shago"){
                        $response = $this->shagoService->vendAirtime($params);
                    } else{ 
                        throw new \Exception("Invalid vendor");
                    }

                    $this->transactionService->createTransaction([
                        'user_id' => $user_id,
                        'reference' => isset($response['data']) ? $response['data']['transactionReference'] : '',
                        'amount'    => $params['amount'],
                        'network_provider'   => $type,
                        'transaction_type' => 'wallet_debit',
                        'description' => $type.' airtime was purchased.'
                    ]);
                    
                    $comissionAmount = $this->commissionService->getCommissionLookup($params['amount']);

                    if(!empty($comissionAmount)) {
                        $userWallet->balance = $userWallet->balance + $comissionAmount->bonus_amount;
                        
                        $trans = $this->transactionService->createTransaction([
                            'user_id' => $user_id,
                            'reference' => 'system-reference'.time(),
                            'amount'    => $comissionAmount->bonus_amount,
                            'transaction_type' => 'comission_top_up',
                            'description' => 'Comission on airtime purchased.'
                        ]);

                        $userWallet->last_transaction_id = $trans->id;
                        $userWallet->save();
                    }

                    return [
                        'status'   => true,
                        'message' => 'Airtime purchase was succesful',
                        'wallet_balance' => $userWallet->balance
                    ];
                    
                } else {
                    return [
                        'status' => false,
                        'message' => 'Insufficient wallet balance'
                    ];
                }
            });
            

        } catch (\Exception $e) {   
            DB::rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

    }
}