<?php

namespace App\Billing;

use App\Billing\PaymentGateway;
use App\Billing\PaymentFailedException;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;
    private $beforeFirstChargeCallback;

    public function __construct()   
    {
        $this->charges = collect();
    }
    
    public function getValidTestToken()
    {
        return "valid-token";
    }

    public function charge($amount, $token)
    {
        if($this->beforeFirstChargeCallback !== null){
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }
        if($token !== $this->getValidTestToken()){  
            throw new PaymentFailedException;
        }
        $this->charges[] = $amount;
    }

    public function newChargesDuring($callback)
    {
        $lastCharge = $this->charges->count();
        $callback($this);
        return $this->charges->slice($lastCharge);
    }
    

    public function totalCharges()
    {
        return $this->charges->sum();
    }

    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }
    
    
    
    

}