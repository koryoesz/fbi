<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Repositories\IAirtimeRepository;
use App\Services\AirtimeService;
use App\Services\TransactionService;
use App\Repositories\ITransactionRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Schema::defaultStringLength(191); 
        $this->app->bind(IAirtimeRepository::class, AirtimeService::class);
        $this->app->bind(ITransactionRepository::class, TransactionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
