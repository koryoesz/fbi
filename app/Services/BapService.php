<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use \Illuminate\Http\Client\RequestException;
class BapService {

    protected $base_url;
    protected $api_key;
    public function __construct() { 
        $this->base_url = config('app.api_vendors.bap.base_url');
        $this->api_key = config('app.api_vendors.bap.api_key');
    }

    public function vendAirtime($params)
    {
        $headers = ['x-api-key' => $this->api_key];
        $api = $this->base_url.'/services/airtime/request';
        $params['agentReference'] = "bap-".time();

        $response = Http::withoutVerifying()->withHeaders($headers)->post($api, $params);

        try {
            if($response->successful()){
                return $response->json();
            } else {
                return $response->json();
            }
        } catch (RequestException $e) {
            \Log::error('HTTP Bap Request Failed', [
                'status' => $e->response->status(),
                'error' => $e->response->body()
            ]);

            return [
                'success' => false,
                'status' => $e->response->status(),
                'error' => $e->response->json() ?? $e->response->body()
            ];
        }
    }
}