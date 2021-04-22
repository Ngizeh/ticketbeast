<?php

namespace App\Providers;

use App\Billing\PaymentGateway;
use App\Billing\StripeGateway;
use App\OrderConfirmationNumber;
use App\RandomOrderConfirmationNumberGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(StripeGateway::class, fn() => new StripeGateway(config('services.stripe.secret')));

        $this->app->bind(PaymentGateway::class, StripeGateway::class);
        $this->app->bind(OrderConfirmationNumber::class, RandomOrderConfirmationNumberGenerator::class);
    }
}
